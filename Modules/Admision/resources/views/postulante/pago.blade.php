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

        @if(!$postulante->cumpleRequisitos())
            {{-- Requisitos NO aprobados: bloquear pago --}}
            <div class="card mt-3" style="border-color: var(--warning); background: rgba(245, 127, 23, 0.05);">
                <div class="card-body" style="text-align:center; padding: 32px;">
                    <div style="font-size: 3rem; margin-bottom: 12px;"></div>
                    <h3 style="color: var(--warning-light); margin-bottom: 8px;">Pago Bloqueado</h3>
                    <p class="text-muted" style="margin-bottom: 16px;">
                        Para habilitar el pago, primero debes subir tus documentos y esperar a que el
                        <strong style="color:var(--text-primary);">administrador los verifique y apruebe</strong>.
                    </p>

                    <div style="background: var(--bg-input); border-radius: var(--radius-sm); padding: 16px; margin-bottom: 20px; text-align: left;">
                        <p style="font-size:0.88rem; margin-bottom: 10px;"><strong>Estado de tus requisitos:</strong></p>
                        <p style="font-size:0.85rem; margin-bottom: 6px;">
                            @if($postulante->archivo_titulo_bachiller)
                                @if($postulante->titulo_bachiller)
                                    Título de Bachiller — <span class="text-success">Aprobado</span>
                                @else
                                    Título de Bachiller — <span style="color: var(--warning-light);">En revisión por el administrador</span>
                                @endif
                            @else
                                Título de Bachiller — <span class="text-danger">No subido</span>
                            @endif
                        </p>
                        <p style="font-size:0.85rem;">
                            @if($postulante->archivo_libreta)
                                @if($postulante->libreta_de_ultimo_anio)
                                    Libreta de Último Año — <span class="text-success">Aprobado</span>
                                @else
                                    Libreta de Último Año — <span style="color: var(--warning-light);">En revisión por el administrador</span>
                                @endif
                            @else
                                Libreta de Último Año — <span class="text-danger">No subido</span>
                            @endif
                        </p>
                    </div>

                    @if(!$postulante->archivo_titulo_bachiller || !$postulante->archivo_libreta)
                        <a href="{{ route('postulante.requisitos') }}" class="btn btn-primary">
                            Ir a Subir Requisitos
                        </a>
                    @else
                        <p class="text-muted" style="font-size:0.82rem;">
                            Tus documentos ya fueron subidos. El administrador los revisará pronto.
                        </p>
                    @endif
                </div>
            </div>
        @elseif($pago && $pago->estaCompletado())
            {{-- Pago ya completado --}}
            <div class="alert alert-success mt-3" style="text-align:center;">
                <strong>Pago Completado</strong><br>
                ID PayPal: {{ $pago->paypal_order_id }}<br>
                Fecha: {{ $pago->fecha_pago?->format('d/m/Y H:i') }}
            </div>
        @else
            {{-- Requisitos aprobados: mostrar opciones de pago --}}
            <div class="alert alert-success mt-3" style="text-align:center;">
                Requisitos verificados. Puedes proceder con el pago.
            </div>

            @if($modoTest)
                <div class="alert alert-warning mt-2">
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

@if($postulante->cumpleRequisitos() && !$modoTest && !($pago && $pago->estaCompletado()))
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
