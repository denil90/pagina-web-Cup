@extends('layouts.app')
@section('title', 'Pago')
@section('header', 'Pago de Inscripción')

@section('content')
<div class="card" style="max-width:600px;margin:0 auto;">
    <div class="card-body">
        <div class="payment-card">
            <h3 style="color:var(--text-primary);">Inscripción al Curso Preuniversitario</h3>
            <div class="payment-amount">Bs. {{ number_format($monto, 2) }}</div>
            <div class="payment-currency">Bolivianos</div>
        </div>

        @if($pago && $pago->estaCompletado())
            <div class="alert alert-success mt-3" style="text-align:center;">
                <strong>Pago Completado</strong><br>
                ID PayPal: {{ $pago->paypal_order_id }}<br>
                Fecha: {{ $pago->fecha_pago?->format('d/m/Y H:i') }}
            </div>
        @else
            @if($modoTest)
                <div class="alert alert-warning mt-3">
                    <strong>Modo Test:</strong> PayPal está en modo sandbox. Puede simular el pago.
                </div>
                <form method="POST" action="{{ route('postulante.pago.simular') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg w-100">Simular Pago (Test)</button>
                </form>
            @else
                <div id="paypal-button-container" class="mt-3"></div>
            @endif
        @endif
    </div>
</div>

@if(!$modoTest && !($pago && $pago->estaCompletado()))
@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ $clientId }}&currency=USD"></script>
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: '{{ number_format($monto / 7, 2) }}' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                fetch('{{ route("postulante.pago.confirmar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ paypal_order_id: data.orderID })
                })
                .then(r => r.json())
                .then(d => { if(d.success) location.reload(); else alert(d.message); });
            });
        }
    }).render('#paypal-button-container');
</script>
@endpush
@endif
@endsection
