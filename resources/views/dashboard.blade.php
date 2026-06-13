@extends('layouts.app')

@section('content')
<div class="py-4" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 800)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Encabezado Bienvenida --}}
        <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-pc-blue dark:text-white tracking-tight">Panel de Control Operativo</h1>
                <div class="flex items-center gap-3">
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Resumen de Métricas y Estado de Fuerza</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="bg-pc-blue/5 dark:bg-pc-blue/10 border border-pc-blue/10 dark:border-pc-blue/20 px-4 py-2 rounded-xl text-pc-blue dark:text-pc-orange font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-clock text-pc-orange"></i> {{ now()->format('d M, Y') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Columna principal (2/3 del ancho) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Tarjetas de resumen métricas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Tarjeta 1 --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border-b-4 border-pc-blue shadow-sm hover:shadow-md transition-all">
                        <div x-show="loading" class="space-y-3">
                            <div class="h-4 w-1/2 skeleton rounded"></div>
                            <div class="h-8 w-3/4 skeleton rounded"></div>
                        </div>
                        <div x-show="!loading" x-cloak>
                            <div class="flex items-center justify-between mb-2">
                                <div class="bg-pc-blue/10 p-1.5 rounded-lg"><i class="fas fa-users text-pc-blue"></i></div>
                                <span class="text-[10px] font-black text-gray-300 dark:text-slate-700 uppercase tracking-widest">Total</span>
                            </div>
                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $totalEmployees }}</p>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Colaboradores</p>
                        </div>
                    </div>

                    {{-- Tarjeta 2 --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border-b-4 border-green-500 shadow-sm hover:shadow-md transition-all">
                        <div x-show="loading" class="space-y-3">
                            <div class="h-4 w-1/2 skeleton rounded"></div>
                            <div class="h-8 w-3/4 skeleton rounded"></div>
                        </div>
                        <div x-show="!loading" x-cloak>
                            <div class="flex items-center justify-between mb-2">
                                <div class="bg-green-100 dark:bg-green-900/20 p-1.5 rounded-lg"><i class="fas fa-user-check text-green-600"></i></div>
                                <span class="text-[10px] font-black text-gray-300 dark:text-slate-700 uppercase tracking-widest">Activos</span>
                            </div>
                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $activeEmployees }}</p>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">En servicio</p>
                        </div>
                    </div>

                    {{-- Tarjeta 3 --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border-b-4 border-pc-orange shadow-sm hover:shadow-md transition-all">
                        <div x-show="loading" class="space-y-3">
                            <div class="h-4 w-1/2 skeleton rounded"></div>
                            <div class="h-8 w-3/4 skeleton rounded"></div>
                        </div>
                        <div x-show="!loading" x-cloak>
                            <div class="flex items-center justify-between mb-2">
                                <div class="bg-orange-100 dark:bg-orange-900/20 p-1.5 rounded-lg"><i class="fas fa-umbrella-beach text-pc-orange"></i></div>
                                <span class="text-[10px] font-black text-gray-300 dark:text-slate-700 uppercase tracking-widest">Alerta</span>
                            </div>
                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $pendingVacations }}</p>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Vacaciones</p>
                        </div>
                    </div>

                    {{-- Tarjeta 4 --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border-b-4 border-pc-red shadow-sm hover:shadow-md transition-all">
                        <div x-show="loading" class="space-y-3">
                            <div class="h-4 w-1/2 skeleton rounded"></div>
                            <div class="h-8 w-3/4 skeleton rounded"></div>
                        </div>
                        <div x-show="!loading" x-cloak>
                            <div class="flex items-center justify-between mb-2">
                                <div class="bg-red-100 dark:bg-red-900/20 p-1.5 rounded-lg"><i class="fas fa-notes-medical text-pc-red"></i></div>
                                <span class="text-[10px] font-black text-gray-300 dark:text-slate-700 uppercase tracking-widest">Médico</span>
                            </div>
                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $pendingLeaves }}</p>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Reposos</p>
                        </div>
                    </div>
                </div>

                {{-- Sección de Gráficos --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="card-pc p-4 dark:bg-slate-900 dark:border-slate-800">
                        <h3 class="text-[11px] font-black text-pc-blue dark:text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-pc-orange"></i> Distribución por Depto.
                        </h3>
                        <div class="flex items-center gap-3">
                            <div class="w-28 h-28 flex-shrink-0">
                                <canvas id="deptChart"></canvas>
                            </div>
                            <div class="flex-1 space-y-1.5 overflow-hidden" id="deptLegend">
                                @php
                                    $colors = ['#0B3B5E','#F97316','#22C55E','#E63946','#6366F1','#8B5CF6','#EC4899','#14B8A6'];
                                @endphp
                                @foreach($departmentNames as $i => $name)
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $colors[$i % 8] }}"></span>
                                    <span class="text-[9px] font-bold text-gray-600 dark:text-gray-400 truncate flex-1">{{ $name }}</span>
                                    <span class="text-[9px] font-black text-gray-800 dark:text-white">{{ $departmentCounts[$i] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-pc p-4">
                        <h3 class="text-[11px] font-black text-pc-blue uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-pc-orange"></i> Estado de Fuerza
                        </h3>
                        <div class="h-48">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Gráfico de Actividad (Asistencias) --}}
                <div class="card-pc p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[11px] font-black text-pc-blue uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-chart-line text-pc-orange"></i> Actividad de Asistencia (30 días)
                        </h3>
                        <span class="text-[9px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold uppercase">En tiempo real</span>
                    </div>
                    <div class="h-48">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>

                {{-- Últimos ingresos --}}
                <div class="card-pc overflow-hidden">
                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-[11px] font-black text-pc-blue uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-user-plus text-pc-orange"></i> Nuevos Integrantes
                        </h3>
                        <a href="{{ route('employees.index') }}" class="text-[10px] font-bold text-pc-blue hover:text-pc-orange transition-colors">Ver todos</a>
                    </div>
                    <div class="p-0">
                        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                            @foreach($latestEmployees->take(3) as $emp)
                                <div class="px-4 py-3 flex items-center gap-3 hover:bg-gray-50/50 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-pc-blue text-white flex shrink-0 items-center justify-center font-black text-[10px] shadow-sm">
                                        {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-gray-800 truncate">{{ $emp->full_name }}</p>
                                        <p class="text-[9px] text-gray-500 font-bold uppercase tracking-tighter truncate">{{ $emp->department->name ?? 'Sin Departamento' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra lateral derecha (1/3 del ancho) --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Widget: Registro de Asistencia Rápido --}}
                @include('partials.attendance-block')

                {{-- Widget: Guardias del Día (NUEVO) --}}
                @include('partials.guardias-today-widget')

                {{-- Widget: Vacaciones Hoy --}}
                <div class="card-pc overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <h3 class="text-[11px] font-black text-pc-blue uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-plane-departure text-pc-orange"></i> En Vacaciones Hoy
                        </h3>
                    </div>
                    <div class="p-3 max-h-48 overflow-y-auto custom-scrollbar">
                        @forelse($employeesOnVacation as $vac)
                            <div class="flex items-center gap-3 mb-2 last:mb-0 p-1.5 rounded-lg hover:bg-pc-blue/5 transition-colors">
                                <div class="w-6 h-6 rounded-full bg-pc-blue/10 flex shrink-0 items-center justify-center text-pc-blue text-[9px] font-black">
                                    {{ strtoupper(substr($vac->employee->first_name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold text-gray-800 truncate">{{ $vac->employee->full_name }}</p>
                                    <div class="flex items-center gap-2 text-[8px] text-gray-400 font-bold">
                                        <span>RETORNA: {{ $vac->end_date->format('d/m') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-3 text-center">
                                <p class="text-[10px] text-gray-400 italic">Todo el personal en planta hoy.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Últimas Asistencias (Tabla compacta) --}}
                <div class="card-pc overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-[11px] font-black text-pc-blue uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-clock-rotate-left text-pc-orange"></i> Actividad Reciente
                        </h3>
                    </div>
                    <div class="overflow-x-auto max-h-48 overflow-y-auto custom-scrollbar">
                        <table class="w-full text-[10px]">
                            <tbody class="divide-y divide-gray-50">
                                @forelse($latestAttendances as $att)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700">{{ $att->employee->first_name }} {{ substr($att->employee->last_name, 0, 1) }}.</td>
                                    <td class="px-3 py-2 text-gray-400">{{ $att->created_at->format('H:i') }}</td>
                                    <td class="px-3 py-2 text-right">
                                        @if($att->check_out)
                                            <span class="text-green-600 font-black tracking-tighter uppercase">Salida</span>
                                        @else
                                            <span class="text-pc-orange font-black tracking-tighter uppercase">Entrada</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-3 text-gray-400 italic">Sin actividad</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Widget: Próximos Retornos (Reposos) --}}
                <div class="card-pc overflow-hidden border-t-pc-red">
                    <div class="px-4 py-3 bg-red-50/50 border-b border-red-100">
                        <h3 class="text-[11px] font-black text-pc-red uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-heart-pulse text-pc-red"></i> Retornos de Reposo
                        </h3>
                    </div>
                    <div class="p-3 space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                        @forelse($endingLeaves as $leave)
                            @php $daysLeft = now()->diffInDays($leave->end_date, false); @endphp
                            <div class="flex flex-col gap-1 p-1.5 rounded-lg border border-transparent hover:border-red-100 hover:bg-red-50/30 transition-all">
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] font-bold text-gray-800">{{ $leave->employee->full_name }}</span>
                                    <span class="text-[9px] font-black {{ $daysLeft <= 3 ? 'text-pc-red animate-pulse' : 'text-pc-orange' }}">
                                        {{ $daysLeft <= 0 ? 'HOY' : 'EN ' . round($daysLeft) . ' DÍAS' }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 h-1 rounded-full overflow-hidden">
                                    <div class="bg-pc-red h-full" style="width: {{ max(10, 100 - ($daysLeft * 10)) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-[10px] text-gray-400 text-center py-2 italic">Sin retornos próximos.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Notificaciones de Entrada / Bienvenida y Seguridad --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para validar la seguridad
        function checkSecurityAnswers() {
            @if(auth()->user() && auth()->user()->securityAnswers()->count() < 4)
                Swal.fire({
                    title: '¡Atención de Seguridad!',
                    text: 'Aún no has configurado tus preguntas de seguridad. Esto es indispensable para poder recuperar tu cuenta en caso de pérdida de credenciales.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Configurar Ahora',
                    cancelButtonText: 'Recordarme más tarde',
                    confirmButtonColor: '#ea580c', // pc-orange
                    cancelButtonColor: '#64748b',
                    background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('profile.edit') }}";
                    }
                });
            @endif
        }

        // Si hay una sesión de inicio de bienvenida reciente, la mostramos
        @if(session('welcome_back'))
            Swal.fire({
                title: '¡Hola de nuevo, {{ auth()->user()->first_name ?? auth()->user()->name }}! 🛡️',
                html: '<div class="space-y-3"><p class="text-xs text-gray-500 font-medium">Qué alegría tenerte de vuelta en el <b>SGTH</b>.</p><p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tu labor diaria en <b>Protección Civil</b> marca la diferencia para salvaguardar la vida de todos. ¡Gracias por tu dedicación!</p><p class="text-[10px] font-black text-pc-orange uppercase tracking-wider mt-4 animate-pulse">¡Te deseamos una excelente y productiva jornada hoy! 🧡</p></div>',
                icon: 'success',
                confirmButtonText: 'Entrar al Panel',
                confirmButtonColor: '#0b3b5e', // pc-blue
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a',
                allowOutsideClick: true
            }).then(() => {
                checkSecurityAnswers();
            });
        @else
            // Si no hay bienvenida, validamos la seguridad directamente
            checkSecurityAnswers();
        @endif
    });
</script>
@endpush

{{-- Datos para Gráficos --}}
<div id="dashboard-data" style="display: none;"
     data-dept-labels='@json($departmentNames)'
     data-dept-counts='@json($departmentCounts)'
     data-status-counts='@json(["Activos" => $activeEmployees, "Inactivos" => $inactiveEmployees, "Reposo" => $reposeEmployees])'
     data-attendance-labels='@json($attendanceDates)'
     data-attendance-data='@json($attendanceCounts)'>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush