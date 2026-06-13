<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_pc.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script>
        if (localStorage.getItem('darkMode') === 'true' || 
            (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-slate-950 transition-colors duration-300">
    <div class="min-h-screen" x-data="{ sidebarOpen: false }">
        <!-- Navbar superior -->
        <nav class="bg-white shadow-md sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Botón Hamburguesa para Móvil -->
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-pc-orange focus:outline-none mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Logo y título -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                            <div class="flex flex-col">
                                <span class="text-xl font-black text-pc-blue leading-none hidden sm:inline">Sistema de Gestión de Talento Humano</span>
                                <span class="text-base font-black text-pc-blue leading-none sm:hidden">SGTH</span>
                                <span class="text-[10px] font-bold text-pc-orange uppercase tracking-widest hidden sm:inline">Plataforma Operativa Integrada</span>
                            </div>
                        </a>
                    </div>

                    <!-- Buscador global y Acciones Rápidas -->
                    <div class="flex-1 flex justify-end items-center gap-4 px-4 lg:px-0">
                        <!-- Logo Gobernación -->
                        <div class="hidden lg:flex items-center mr-4 border-r border-gray-200 dark:border-slate-800 pr-6">
                            <img src="{{ asset('images/logo_ciudad_bolivar.png') }}" alt="Gobernación Bolívar" class="h-14 w-auto transition-transform hover:scale-105 drop-shadow-sm dark:bg-white/95 dark:p-1.5 dark:rounded-xl">
                        </div>
                        <form method="GET" action="{{ route('employees.index') }}" class="w-full max-w-xs hidden lg:block">
                            <div class="relative">
                                <div class="absolute z-10 inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="global_search" value="{{ request('search') }}" 
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-slate-800 rounded-lg leading-5 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pc-orange focus:border-transparent sm:text-sm transition-all"
                                       placeholder="Buscar...">
                            </div>
                        </form>

                        <!-- Toggle Modo Oscuro -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                                class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400 hover:text-pc-orange dark:hover:text-pc-orange transition-all">
                            <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                        </button>

                        <!-- Manuales de Ayuda -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400 hover:text-pc-blue transition-all">
                                <i class="fas fa-circle-question"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition x-cloak
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden z-50">
                                <div class="p-4 border-b border-gray-100 dark:border-slate-800">
                                    <h4 class="text-[10px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">Centro de Ayuda</h4>
                                </div>
                                <div class="py-2">
                                    <a href="#" onclick="alert('El Manual de Usuario se encuentra en etapa de redacción y diseño. Estará disponible próximamente.'); return false;" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                                        <div class="w-8 h-8 rounded-lg bg-pc-blue/10 dark:bg-pc-blue/20 text-pc-blue flex items-center justify-center group-hover:bg-pc-blue group-hover:text-white transition-all">
                                            <i class="fas fa-book-open text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-gray-800 dark:text-gray-200">Manual de Usuario</p>
                                            <p class="text-[8px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Nivel Operativo</p>
                                        </div>
                                    </a>
                                    <a href="#" onclick="alert('El Manual Técnico se encuentra en etapa de redacción y diseño. Estará disponible próximamente.'); return false;" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                                        <div class="w-8 h-8 rounded-lg bg-pc-orange/10 dark:bg-pc-orange/20 text-pc-orange flex items-center justify-center group-hover:bg-pc-orange group-hover:text-white transition-all">
                                            <i class="fas fa-laptop-code text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-gray-800 dark:text-gray-200">Manual Técnico</p>
                                            <p class="text-[8px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Nivel Administrativo</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Centro de Notificaciones -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400 hover:text-pc-orange transition-all relative">
                                <i class="fas fa-bell"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-pc-red rounded-full border-2 border-white dark:border-slate-800 animate-pulse"></span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition x-cloak
                                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden z-50">
                                <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
                                    <h4 class="text-[10px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">Notificaciones ({{ auth()->user()->unreadNotifications->count() }})</h4>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-[9px] font-black text-pc-orange uppercase">Limpiar todo</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @forelse(auth()->user()->notifications->take(10) as $notification)
                                        <div class="p-4 border-b border-gray-50 dark:border-slate-800/50 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50/20 dark:bg-pc-orange/5' }}">
                                            <div class="flex gap-3">
                                                <div class="w-8 h-8 rounded-full bg-pc-orange/10 flex items-center justify-center text-pc-orange">
                                                    <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} text-xs"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-[10px] font-bold text-gray-900 dark:text-gray-100">{{ $notification->data['title'] }}</p>
                                                    <p class="text-[9px] text-gray-500 dark:text-gray-400">{{ $notification->data['message'] }}</p>
                                                    <p class="text-[8px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-8 text-center">
                                            <i class="fas fa-bell-slash text-gray-200 dark:text-slate-800 text-3xl mb-2"></i>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sin alertas nuevas</p>
                                        </div>
                                    @endforelse
                                </div>
                                <a href="#" class="block p-3 text-center text-[9px] font-black text-pc-blue dark:text-pc-orange bg-gray-50 dark:bg-slate-800/50 uppercase tracking-widest hover:bg-gray-100 transition-colors">Historial de Alertas</a>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown de usuario -->
                    <div class="flex items-center ml-4">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-black rounded-md text-gray-500 dark:text-gray-400 hover:text-pc-blue focus:outline-none transition ease-in-out duration-150">
                                    <div class="flex flex-col items-end mr-3">
                                        <span class="text-xs text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</span>
                                        <span class="text-[9px] font-black text-pc-orange uppercase tracking-widest leading-none">
                                            {{ Auth::user()->roles->first()->name ?? 'Usuario' }}
                                        </span>
                                    </div>
                                    <div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center text-pc-blue dark:text-pc-orange font-black text-xs border border-gray-200 dark:border-slate-700 shadow-sm transition-transform hover:scale-105">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        @endif
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Backdrop para móvil -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-30 lg:hidden"
             x-cloak>
        </div>

        <div class="flex">
            @include('layouts.sidebar')
            <div class="flex-1 min-w-0 overflow-hidden">
                {{-- Breadcrumbs --}}
                <div class="px-6 py-4 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 transition-colors">
                    @yield('breadcrumbs')
                </div>
                
                <div class="p-6">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert2: Notificaciones de sesión --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: '{{ addslashes(session('success')) }}',
                confirmButtonColor: '#F97316',
                confirmButtonText: 'Aceptar',
                timer: 4000,
                timerProgressBar: true,
                showClass: { popup: 'animate__animated animate__fadeInDown' },
                customClass: { popup: 'swal-popup-custom' }
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ addslashes(session('error')) }}',
                confirmButtonColor: '#E63946',
                confirmButtonText: 'Entendido',
                customClass: { popup: 'swal-popup-custom' }
            });
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: '{{ addslashes(session('warning')) }}',
                confirmButtonColor: '#F97316',
                confirmButtonText: 'Entendido',
                customClass: { popup: 'swal-popup-custom' }
            });
        });
    </script>
    @endif

    @if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: '{{ addslashes(session('info')) }}',
                confirmButtonColor: '#0B3B5E',
                confirmButtonText: 'Entendido',
                customClass: { popup: 'swal-popup-custom' }
            });
        });
    </script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        // ============================================================
        // CONFIRMACIÓN GLOBAL DE ELIMINACIÓN CON SWEETALERT2
        // Intercepta cualquier formulario con class="confirm-delete"
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form.confirm-delete').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const label = form.dataset.label || 'este registro';
                    Swal.fire({
                        icon: 'warning',
                        title: '¿Estás seguro?',
                        html: 'Estás a punto de eliminar <strong>' + label + '</strong>.<br>Esta acción <strong>no se puede deshacer</strong>.',
                        showCancelButton: true,
                        confirmButtonColor: '#E63946',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Sí, eliminar',
                        cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar',
                        focusCancel: true,
                        customClass: { popup: 'swal-popup-custom' }
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.dataset.submitting = '';
                            form.submit();
                        }
                    });
                });
            });
        });

        // Prevención global de doble-clic (Double Submit)
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM' && !e.target.classList.contains('no-double-click') && !e.target.classList.contains('confirm-delete')) {
                if (e.target.dataset.submitting) {
                    e.preventDefault();
                    return;
                }
                e.target.dataset.submitting = 'true';
                const btn = e.target.querySelector('button[type="submit"], input[type="submit"]');
                if (btn) {
                    setTimeout(() => {
                        btn.disabled = true;
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                    }, 10);
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>