<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema de Cursos Preuniversitarios - Facultad de Informática y Computación">
    <title>@yield('title', 'Sistema CUP') - FIC</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-layout">
        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">🎓 CUP - FICCT</div>
                <div class="sidebar-subtitle">Curso Preuniversitario</div>
            </div>

            <nav class="sidebar-nav">
                @auth
                    @if(Auth::user()->esAdministrador() || Auth::user()->esDocente())
                        {{-- Menú Administrador/Docente --}}
                        <div class="nav-section-title">Principal</div>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <span class="icon"></span> Dashboard
                        </a>

                        <div class="nav-section-title">Gestión Académica</div>
                        <a href="{{ route('admin.gestiones.index') }}" class="nav-link {{ request()->routeIs('admin.gestiones.*') ? 'active' : '' }}">
                            <span class="icon"></span> Gestiones
                        </a>
                        <a href="{{ route('admin.carreras.index') }}" class="nav-link {{ request()->routeIs('admin.carreras.*') ? 'active' : '' }}">
                            <span class="icon"></span> Carreras
                        </a>
                        <a href="{{ route('admin.materias.index') }}" class="nav-link {{ request()->routeIs('admin.materias.*') ? 'active' : '' }}">
                            <span class="icon"></span> Materias
                        </a>

                        <div class="nav-section-title">Infraestructura</div>
                        <a href="{{ route('admin.grupos.index') }}" class="nav-link {{ request()->routeIs('admin.grupos.*') ? 'active' : '' }}">
                            <span class="icon"></span> Grupos
                        </a>
                        <a href="{{ route('admin.aulas.index') }}" class="nav-link {{ request()->routeIs('admin.aulas.*') ? 'active' : '' }}">
                            <span class="icon"></span> Aulas
                        </a>
                        <a href="{{ route('admin.horarios.index') }}" class="nav-link {{ request()->routeIs('admin.horarios.*') ? 'active' : '' }}">
                            <span class="icon"></span> Horarios
                        </a>
                        <a href="{{ route('admin.turnos.index') }}" class="nav-link {{ request()->routeIs('admin.turnos.*') ? 'active' : '' }}">
                            <span class="icon"></span> Turnos
                        </a>

                        <div class="nav-section-title">Personal</div>
                        <a href="{{ route('admin.docentes.index') }}" class="nav-link {{ request()->routeIs('admin.docentes.*') ? 'active' : '' }}">
                            <span class="icon"></span> Docentes
                        </a>
                        <a href="{{ route('admin.postulantes.index') }}" class="nav-link {{ request()->routeIs('admin.postulantes.*') ? 'active' : '' }}">
                            <span class="icon"></span> Postulantes
                        </a>

                        <div class="nav-section-title">Evaluación</div>
                        <a href="{{ route('admin.notas.index') }}" class="nav-link {{ request()->routeIs('admin.notas.*') ? 'active' : '' }}">
                            <span class="icon"></span> Notas
                        </a>
                        <a href="{{ route('admin.admision.index') }}" class="nav-link {{ request()->routeIs('admin.admision.*') ? 'active' : '' }}">
                            <span class="icon"></span> Admisión
                        </a>
                        <a href="{{ route('admin.reportes.index') }}" class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                            <span class="icon"></span> Reportes
                        </a>
                    @elseif(Auth::user()->esPostulante())
                        {{-- Menú Postulante --}}
                        <div class="nav-section-title">Mi CUP</div>
                        <a href="{{ route('postulante.dashboard') }}" class="nav-link {{ request()->routeIs('postulante.dashboard') ? 'active' : '' }}">
                            <span class="icon"></span> Mi Panel
                        </a>
                        <a href="{{ route('postulante.requisitos') }}" class="nav-link {{ request()->routeIs('postulante.requisitos') ? 'active' : '' }}">
                            <span class="icon"></span> Requisitos
                        </a>
                        <a href="{{ route('postulante.pago') }}" class="nav-link {{ request()->routeIs('postulante.pago') ? 'active' : '' }}">
                            <span class="icon"></span> Pago
                        </a>
                        <a href="{{ route('postulante.notas') }}" class="nav-link {{ request()->routeIs('postulante.notas') ? 'active' : '' }}">
                            <span class="icon"></span> Mis Notas
                        </a>
                        <a href="{{ route('postulante.resultados') }}" class="nav-link {{ request()->routeIs('postulante.resultados') ? 'active' : '' }}">
                            <span class="icon"></span> Resultados
                        </a>
                    @endif
                @endauth
            </nav>

            @auth
                <div class="sidebar-footer">
                    <div class="user-info">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}{{ strtoupper(substr(Auth::user()->apellidos, 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-name">{{ Auth::user()->nombre }} {{ Auth::user()->apellidos }}</div>
                            <div class="user-role">{{ Auth::user()->rol }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm w-100">Cerrar Sesión</button>
                    </form>
                </div>
            @endauth
        </aside>

        {{-- Main Content --}}
        <main class="main-content">
            <div class="content-header">
                <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
                <h1>@yield('header', 'Dashboard')</h1>
                <div>@yield('header-actions')</div>
            </div>

            <div class="content-body fade-in">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
