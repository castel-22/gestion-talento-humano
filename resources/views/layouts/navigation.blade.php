<nav class="bg-pc-blue/95 backdrop-blur-xl border-b border-pc-orange/30 shadow-[0_10px_30px_rgba(11,59,94,0.3)] sticky top-0 z-50 overflow-x-auto lg:overflow-visible transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            {{-- Logo e Identidad --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center group">
                    <img src="{{ asset('images/logo_pc.png') }}" alt="Protección Civil"
                         class="h-12 w-auto rounded-xl bg-white p-1 shadow-lg group-hover:scale-105 transition-transform duration-300">
                </a>
                
                {{-- Enlaces Primarios con Iconos --}}
                <div class="hidden lg:flex space-x-1">
                    @php
                        $navItems = [
                            ['route' => 'dashboard', 'icon' => 'fas fa-th-large', 'label' => 'Panel'],
                            ['route' => 'users.index', 'icon' => 'fas fa-users-cog', 'label' => 'Usuarios', 'role' => 'administrador'],
                            ['route' => 'departments.index', 'icon' => 'fas fa-sitemap', 'label' => 'Unidades'],
                            ['route' => 'employees.index', 'icon' => 'fas fa-id-card', 'label' => 'Personal'],
                            ['route' => 'attendances.index', 'icon' => 'fas fa-user-check', 'label' => 'Asistencias'],
                            ['route' => 'vacations.index', 'icon' => 'fas fa-umbrella-beach', 'label' => 'Vacaciones'],
                            ['route' => 'leaves.index', 'icon' => 'fas fa-notes-medical', 'label' => 'Reposos'],
                            ['route' => 'deployments.index', 'icon' => 'fas fa-truck-moving', 'label' => 'Misiones'],
                            ['route' => 'guard-rotations.index', 'icon' => 'fas fa-shield-halved', 'label' => 'Guardias'],
                            ['route' => 'reports.index', 'icon' => 'fas fa-chart-pie', 'label' => 'Reportes'],
                        ];
                    @endphp

                    @foreach($navItems as $item)
                        @if(!isset($item['role']) || Auth::user()->hasRole($item['role']))
                            <a href="{{ route($item['route']) }}" 
                               class="inline-flex flex-col items-center justify-center px-3 pt-1 border-b-4 text-[9px] font-black uppercase tracking-widest leading-5 transition-all duration-200
                                      {{ request()->routeIs(explode('.', $item['route'])[0] . '.*') ? 'border-pc-orange text-white bg-white/10 shadow-[inset_0_-2px_10px_rgba(249,115,22,0.2)]' : 'border-transparent text-blue-100 hover:text-white hover:bg-white/10 hover:border-white/30 hover:shadow-inner' }}">
                                <i class="{{ $item['icon'] }} text-lg mb-1 opacity-80"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Menú de Usuario y Logos Secundarios --}}
            <div class="flex items-center gap-4">
                {{-- Logo Gobernación --}}
                <div class="hidden xl:flex shrink-0 items-center">
                    <img src="{{ asset('images/logo_ciudad_bolivar.png') }}" 
                         alt="Gobernación de Bolívar"
                         class="h-10 w-auto rounded-lg bg-white p-1 opacity-90">
                </div>

                <div class="h-10 w-[1px] bg-white/10 hidden lg:block"></div>

                {{-- Perfil e Identidad de Rol --}}
                <div class="flex items-center gap-3 ml-2">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-[10px] font-black text-white uppercase tracking-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[8px] font-bold text-pc-orange uppercase tracking-widest">{{ Auth::user()->getRoleNames()->first() }}</span>
                    </div>
                    
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="w-10 h-10 rounded-xl bg-gradient-to-br from-white/20 to-white/5 text-white flex items-center justify-center hover:from-pc-orange hover:to-orange-500 transition-all border border-white/20 shadow-lg group">
                                <span class="font-black text-xs group-hover:scale-110 transition-transform">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="p-2">
                                <x-dropdown-link :href="route('profile.edit')" class="rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-pc-blue hover:text-white transition-all">
                                    <i class="fas fa-user-edit mr-2 opacity-50"></i> Mi Perfil
                                </x-dropdown-link>
                                <div class="my-1 border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" 
                                                     onclick="event.preventDefault(); this.closest('form').submit();"
                                                     class="rounded-lg font-black text-[10px] uppercase tracking-widest text-pc-red hover:bg-pc-red hover:text-white transition-all">
                                        <i class="fas fa-power-off mr-2 opacity-50"></i> Cerrar Sesión
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    {{-- Navegación Móvil (Horizontal Scroll) --}}
    <div class="lg:hidden flex border-t border-white/5 bg-black/10 overflow-x-auto no-scrollbar">
        @foreach($navItems as $item)
            @if(!isset($item['role']) || Auth::user()->hasRole($item['role']))
                <a href="{{ route($item['route']) }}" class="flex-shrink-0 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-blue-100 hover:text-white border-r border-white/5 {{ request()->routeIs(explode('.', $item['route'])[0] . '.*') ? 'bg-pc-orange text-white' : '' }}">
                    <i class="{{ $item['icon'] }} mr-2"></i> {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</nav>