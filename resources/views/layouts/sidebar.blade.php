<div class="sidebar transform transition-transform duration-300 lg:translate-x-0"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    {{-- Cabecera con logo institucional --}}
    <div class="p-5 border-b border-white/10 bg-gradient-to-b from-white/5 to-transparent">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <img src="{{ asset('images/logo_pc.png') }}" alt="Protección Civil"
                     class="w-16 h-16 rounded-full bg-white p-0.5 shadow-lg object-contain ring-4 ring-pc-orange/30">
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-black text-white leading-tight truncate">SGTH</h2>
                    <p class="text-[11px] font-bold text-pc-orange/90 uppercase tracking-widest">Protección Civil</p>
                </div>
            </div>
            <!-- Botón para Cerrar Sidebar en Móvil/Tablet -->
            <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white focus:outline-none shrink-0 p-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>

    {{-- Navegación --}}
    <nav class="mt-4 flex-1 overflow-y-auto">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        {{-- Gestión de Personal --}}
        <div class="sidebar-section-title">
            <span class="tracking-wider text-[0.65rem] text-pc-orange/70 font-bold uppercase px-1.5">
                Gestión de Personal
            </span>
        </div>

        @can('viewAny', App\Models\Employee::class)
            <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="fas fa-id-badge"></i>
                <span>Empleados</span>
            </a>
        @endcan

        @can('viewAny', App\Models\Department::class)
            <a href="{{ route('departments.index') }}" class="sidebar-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <i class="fas fa-sitemap"></i>
                <span>Departamentos</span>
            </a>
        @endcan

        @can('viewAny', App\Models\Vacation::class)
            {{-- Árbol tipo directorio Windows 10 --}}
            <div x-data="{ open: {{ request()->routeIs('vacations.*') ? 'true' : 'false' }} }" class="mb-0.5">

                {{-- Menú principal: Vacaciones (click abre/cierra el árbol) --}}
                <button type="button"
                        @click="open = !open"
                        class="sidebar-link w-full text-left {{ request()->routeIs('vacations.*') ? 'active' : '' }}">
                    <i class="fas fa-umbrella-beach"></i>
                    <span class="flex-1">Vacaciones</span>
                    <i :class="open ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                       class="text-white/30 text-[9px]"></i>
                </button>

                {{-- Sub-menús anidados --}}
                <div x-show="open" x-cloak
                     class="mt-0.5 pb-1"
                     style="margin-left:1.1rem; border-left:1px dotted rgba(255,255,255,0.25);">

                    <a href="{{ route('vacations.index') }}"
                       class="sidebar-link py-1.5 text-[0.72rem] relative {{ request()->routeIs('vacations.index') ? 'active' : '' }}"
                       style="padding-left:1.1rem;">
                        <span style="position:absolute;left:0;top:50%;width:10px;height:1px;border-top:1px dotted rgba(255,255,255,0.25);display:block;"></span>
                        <i class="fas fa-list-check text-[0.72rem]"></i>
                        <span>Registro General</span>
                    </a>

                    <a href="{{ route('vacations.contingencies') }}"
                       class="sidebar-link py-1.5 text-[0.72rem] relative {{ request()->routeIs('vacations.contingencies') ? 'active' : '' }}"
                       style="padding-left:1.1rem;">
                        <span style="position:absolute;left:0;top:50%;width:10px;height:1px;border-top:1px dotted rgba(255,255,255,0.25);display:block;"></span>
                        <i class="fas fa-clipboard-list text-[0.72rem]"></i>
                        <span>Planes de Contingencia</span>
                    </a>

                    <a href="{{ route('vacations.calendar') }}"
                       class="sidebar-link py-1.5 text-[0.72rem] relative {{ request()->routeIs('vacations.calendar') ? 'active' : '' }}"
                       style="padding-left:1.1rem;">
                        <span style="position:absolute;left:0;top:50%;width:10px;height:1px;border-top:1px dotted rgba(255,255,255,0.25);display:block;"></span>
                        <i class="fas fa-calendar-days text-[0.72rem]"></i>
                        <span>Calendario Vacacional</span>
                    </a>

                </div>
            </div>
        @endcan

        @can('viewAny', App\Models\Leave::class)
            <a href="{{ route('leaves.index') }}" class="sidebar-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                <i class="fas fa-file-medical"></i>
                <span>Reposos Médicos</span>
            </a>
        @endcan

        <a href="{{ route('guard-rotations.index') }}" class="sidebar-link {{ request()->routeIs('guard-rotations.*') ? 'active' : '' }}">
            <i class="fas fa-shield-halved"></i>
            <span>Guardias 24×72</span>
        </a>

        {{-- Operaciones --}}
        <div class="sidebar-section-title">
            <span class="tracking-wider text-[0.65rem] text-pc-orange/70 font-bold uppercase px-1.5">
                Operaciones
            </span>
        </div>

        @can('viewAny', App\Models\Deployment::class)
            <a href="{{ route('deployments.index') }}" class="sidebar-link {{ request()->routeIs('deployments.*') ? 'active' : '' }}">
                <i class="fas fa-truck-fast"></i>
                <span>Despliegues Operativos</span>
            </a>
        @endcan

        @can('viewAny', App\Models\Attendance::class)
            <a href="{{ route('attendances.index') }}" class="sidebar-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                <i class="fas fa-fingerprint"></i>
                <span>Control de Asistencia</span>
            </a>
        @endcan

        <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Centro de Reportes</span>
        </a>

        {{-- Administración --}}
        <div class="sidebar-section-title">
            <span class="tracking-wider text-[0.65rem] text-pc-orange/70 font-bold uppercase px-1.5">
                Administración
            </span>
        </div>

        @can('viewAny', App\Models\User::class)
            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Usuarios del Sistema</span>
            </a>
            @role('administrador')
            <a href="{{ route('activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Auditoría de Acciones</span>
            </a>
            @endrole
        @endcan

        @if (Route::has('permissions.index'))
            <a href="{{ route('permissions.index') }}" class="sidebar-link">
                <i class="fas fa-key"></i>
                <span>Permisos y Roles</span>
            </a>
        @endif

        @can('viewAny', App\Models\Backup::class)
            <a href="{{ route('backups.index') }}" class="sidebar-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                <i class="fas fa-server"></i>
                <span>Respaldos del Sistema</span>
            </a>
        @endcan
    </nav>

    {{-- Pie del sidebar --}}
    <div class="p-4 border-t border-white/10 text-center">
        <p class="text-[0.6rem] text-white/40 tracking-wide mb-1">
            &copy; {{ date('Y') }} Protección Civil
        </p>
        <p class="motto-text text-[6px]">"Solo queremos salvar vidas"</p>
    </div>
</div>