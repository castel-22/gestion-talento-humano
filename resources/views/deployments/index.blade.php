@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; width: 100%; z-index: 1; border-radius: 0; border: none; }
    .leaflet-layer, .leaflet-control-zoom-in, .leaflet-control-zoom-out, .leaflet-control-attribution {
        filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);
    }
    .light .leaflet-layer, .light .leaflet-control-zoom-in, .light .leaflet-control-zoom-out, .light .leaflet-control-attribution {
        filter: none;
    }
</style>
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
@endpush

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 dark:text-gray-400 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Control de Despliegues</span>
            </div>
        </li>
    </ol>
</nav>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('map').setView([8.1283, -63.5497], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const deployments = @json($allDeployments ?? []);
        const markers = [];
        const deploymentsBaseUrl = "{{ rtrim(route('deployments.index'), '/') }}";

        // Iconos personalizados por estado (gota invertida usando FontAwesome)
        const createIcon = (colorHex) => L.divIcon({
            className: 'custom-pin-icon',
            html: `<div style="color: ${colorHex}; font-size: 34px; text-shadow: 2px 3px 5px rgba(0,0,0,0.3); transform: translateY(-5px); text-align: center;"><i class="fas fa-map-marker-alt"></i></div>`,
            iconSize: [34, 34], iconAnchor: [17, 34], popupAnchor: [0, -34]
        });

        const iconEn = createIcon('#22c55e'); // Verde (En Curso)
        const iconProg = createIcon('#1e40af'); // Azul (Programado)

        deployments.forEach(dep => {
            if (dep.latitude && dep.longitude) {
                const icon = dep.status === 'en_curso' ? iconEn : iconProg;
                const marker = L.marker([dep.latitude, dep.longitude], { icon }).addTo(map);
                marker.bindPopup(`
                    <div style="padding:12px;min-width:190px;font-family:inherit">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                            <span style="width:8px;height:8px;border-radius:50%;background:${dep.status === 'en_curso' ? '#22c55e' : '#1e40af'};display:inline-block"></span>
                            <span style="font-size:9px;font-weight:900;color:#1e40af;text-transform:uppercase;letter-spacing:0.08em">${dep.place}</span>
                        </div>
                        <p style="font-size:9px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;border-bottom:1px solid #f3f4f6;padding-bottom:8px">${dep.division || 'Sin división'}</p>
                        <a href="${deploymentsBaseUrl}/${dep.id}" style="display:block;text-align:center;background:#1e40af;color:white;font-size:9px;font-weight:900;padding:8px;border-radius:10px;text-transform:uppercase;letter-spacing:0.1em;text-decoration:none">
                            Seguimiento Táctico
                        </a>
                    </div>
                `);
                markers.push(marker);
            }
        });

        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.15));
        }

        document.getElementById('map').classList.remove('skeleton');
    });
</script>
@endpush
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ══ CABECERA ══════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pc-blue/10 dark:bg-pc-blue/20 flex items-center justify-center">
                    <i class="fas fa-truck-moving text-pc-blue"></i>
                </div>
                Operaciones en Campo
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">
                Monitoreo de despliegues operativos y misiones especiales
            </p>
        </div>
        <a href="{{ route('deployments.create') }}"
           class="bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-6 py-3.5 rounded-xl shadow-lg shadow-blue-100/50 transition-all flex items-center gap-2 shrink-0">
            <i class="fas fa-plus"></i> Nueva Misión
        </a>
    </div>

    {{-- ══ MAPA Y KPIs INTEGRADOS ═════════════════════════════════ --}}
    <div class="card-pc p-0 border-t-4 border-pc-blue overflow-hidden shadow-2xl shadow-blue-500/10 dark:bg-slate-900 dark:border-slate-800">
        {{-- KPIs Compactos Header --}}
        <div class="bg-white dark:bg-slate-900 px-5 py-3 border-b border-gray-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-[12px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-lg"></i>
                    <span class="hidden sm:inline">Mapa Operativo</span>
                </span>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                {{-- Stats compactos --}}
                <div class="flex items-center gap-2" title="Programados">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-pc-blue text-[11px]"></i>
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[14px] font-black text-pc-blue leading-none">{{ $stats['programados'] }}</span>
                        <span class="text-[8px] font-bold text-gray-400 uppercase">Prog</span>
                    </div>
                </div>

                <div class="flex items-center gap-2" title="En Curso">
                    <div class="w-7 h-7 rounded-lg bg-green-50 dark:bg-green-500/10 flex items-center justify-center relative">
                        <i class="fas fa-satellite-dish text-green-500 text-[11px]"></i>
                        @if($stats['en_curso'] > 0)<span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-green-500 rounded-full animate-ping"></span>@endif
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[14px] font-black text-green-500 leading-none">{{ $stats['en_curso'] }}</span>
                        <span class="text-[8px] font-bold text-gray-400 uppercase">Activos</span>
                    </div>
                </div>

                <div class="flex items-center gap-2" title="Personal Activo">
                    <div class="w-7 h-7 rounded-lg bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center">
                        <i class="fas fa-users text-pc-orange text-[11px]"></i>
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[14px] font-black text-pc-orange leading-none">{{ $stats['personal_hoy'] }}</span>
                        <span class="text-[8px] font-bold text-gray-400 uppercase">Efectivos</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-2" title="Finalizados / Cancelados">
                    <div class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                        <i class="fas fa-flag-checkered text-gray-400 text-[11px]"></i>
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[14px] font-black text-gray-400 leading-none">{{ $stats['finalizados'] }}</span>
                        <span class="text-[8px] font-bold text-gray-400 uppercase">Fin</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="map" class="skeleton"></div>
    </div>

    {{-- ══ FILTROS ════════════════════════════════════════════════ --}}
    <div class="card-pc p-6 border-t-4 border-pc-blue bg-gray-50/50 dark:bg-slate-900 dark:border-slate-800">
        <form method="GET" action="{{ route('deployments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="label-pc">Estatus Operativo</label>
                <select name="status" class="input-pc text-[10px] font-black py-2.5">
                    <option value="">TODAS LAS MISIONES</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ strtoupper(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="label-pc">División o Destacamento</label>
                <input type="text" name="division" value="{{ request('division') }}" placeholder="Buscar por división..." class="input-pc py-2.5">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-pc-orange text-white font-black text-[10px] uppercase py-3.5 rounded-xl hover:bg-orange-600 transition-all shadow-md shadow-orange-100">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
                <a href="{{ route('deployments.index') }}"
                   class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-400 p-3.5 rounded-xl hover:text-pc-blue transition-all shadow-sm">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- ══ LISTADO DE MISIONES ════════════════════════════════════ --}}
    <div class="space-y-3">
        @forelse($deployments as $deployment)
        @php
            $status = $deployment->computeStatus();
            $statusConfig = [
                'programado' => ['bar' => 'bg-pc-blue',   'badge' => 'bg-blue-100 text-blue-700 border-blue-200',   'label' => 'Programado', 'ping' => false],
                'en_curso'   => ['bar' => 'bg-green-500', 'badge' => 'bg-green-100 text-green-700 border-green-200', 'label' => 'En Curso',   'ping' => true],
                'finalizado' => ['bar' => 'bg-gray-400',  'badge' => 'bg-gray-100 text-gray-600 border-gray-200',   'label' => 'Finalizado', 'ping' => false],
                'cancelado'  => ['bar' => 'bg-pc-red',    'badge' => 'bg-red-100 text-red-600 border-red-200',      'label' => 'Cancelado',  'ping' => false],
            ];
            $sc = $statusConfig[$status] ?? $statusConfig['programado'];

            // Cálculo de progreso temporal
            $start = $deployment->start_datetime;
            $end   = $deployment->end_datetime;
            $showProgress = !$deployment->is_indefinite && $end && in_array($status, ['en_curso', 'finalizado']);
            $progress = 0;
            if ($showProgress) {
                $total   = max(1, $start->diffInMinutes($end));
                $elapsed = max(0, min($start->diffInMinutes(now()), $total));
                $progress = (int) round(($elapsed / $total) * 100);
            }
        @endphp

        <div class="card-pc p-0 overflow-hidden group hover:border-pc-blue dark:hover:border-blue-500 transition-all dark:bg-slate-900 dark:border-slate-800">
            <div class="flex">
                {{-- Barra lateral de color --}}
                <div class="w-1.5 {{ $sc['bar'] }} flex-shrink-0"></div>

                <div class="p-5 flex-1 flex flex-col md:flex-row md:items-center gap-5 min-w-0">

                    {{-- Info principal --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            {{-- Badge estado con ping --}}
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[8px] font-black rounded-lg uppercase tracking-widest border {{ $sc['badge'] }}">
                                @if($sc['ping'])
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                                    </span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full {{ $sc['bar'] }}"></span>
                                @endif
                                {{ $sc['label'] }}
                            </div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                #{{ $deployment->id }} · {{ $deployment->division ?: 'Sin división' }}
                            </span>
                        </div>

                        <h3 class="text-sm font-black text-pc-blue dark:text-white uppercase truncate group-hover:text-pc-orange transition-colors">
                            <i class="fas fa-location-dot mr-2 text-pc-orange/50 text-xs"></i>{{ $deployment->place }}
                        </h3>

                        @if($deployment->reason)
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tight mt-1 truncate">
                                {{ $deployment->reason }}
                            </p>
                        @endif

                        {{-- Barra de progreso temporal --}}
                        @if($showProgress)
                            <div class="mt-3 max-w-xs">
                                <div class="flex justify-between mb-1">
                                    <span class="text-[8px] font-bold text-gray-400">{{ $start->format('d/m H:i') }}</span>
                                    <span class="text-[8px] font-bold {{ $status === 'en_curso' ? 'text-green-600' : 'text-gray-400' }}">{{ $progress }}%</span>
                                    <span class="text-[8px] font-bold text-gray-400">{{ $end->format('d/m H:i') }}</span>
                                </div>
                                <div class="h-1.5 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full {{ $sc['bar'] }} rounded-full transition-all duration-500 {{ $status === 'en_curso' ? 'animate-pulse' : '' }}"
                                         style="width: {{ max(2, $progress) }}%"></div>
                                </div>
                            </div>
                        @elseif($deployment->is_indefinite && $status === 'en_curso')
                            <span class="inline-flex items-center gap-1 mt-2 text-[8px] font-black text-pc-red uppercase">
                                <i class="fas fa-infinity text-[7px]"></i> Misión Indefinida
                            </span>
                        @endif
                    </div>

                    {{-- Fechas --}}
                    <div class="flex flex-col gap-1 md:w-44 shrink-0">
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt text-pc-blue text-[10px] w-3"></i>
                            <span class="text-[10px] font-black text-gray-700 dark:text-gray-300">{{ $deployment->start_datetime->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="far fa-clock text-gray-300 text-[10px] w-3"></i>
                            @if($deployment->is_indefinite)
                                <span class="text-[9px] font-black text-pc-red italic">Indefinida</span>
                            @else
                                <span class="text-[10px] font-black text-gray-500 dark:text-gray-400">
                                    {{ $deployment->end_datetime?->format('d/m/Y H:i') ?? 'En curso' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Personal + Acciones --}}
                    <div class="flex items-center gap-5 shrink-0">
                        <div class="flex flex-col items-center">
                            <span class="text-xl font-black text-pc-blue dark:text-white">{{ $deployment->participants->count() }}</span>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Personal</span>
                        </div>
                        <div class="flex gap-1.5">
                            <a href="{{ route('deployments.show', $deployment) }}" title="Ver detalle"
                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-slate-800 text-pc-blue hover:bg-pc-blue hover:text-white transition-all shadow-sm border border-transparent hover:border-pc-blue">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('deployments.edit', $deployment) }}" title="Editar"
                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-slate-800 text-indigo-400 hover:bg-indigo-400 hover:text-white transition-all shadow-sm border border-transparent hover:border-indigo-400">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-24 bg-gray-50/50 dark:bg-slate-800/30 rounded-3xl border border-dashed border-gray-200 dark:border-slate-700">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-route text-gray-200 dark:text-slate-600 text-3xl"></i>
            </div>
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Sin despliegues registrados</p>
            <a href="{{ route('deployments.create') }}"
               class="inline-flex items-center gap-2 mt-4 bg-pc-blue text-white text-[10px] font-black uppercase px-5 py-2.5 rounded-xl hover:bg-blue-800 transition-all shadow-md shadow-blue-100">
                <i class="fas fa-plus"></i> Crear primera misión
            </a>
        </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div class="flex items-center justify-between">
        <p class="text-[10px] font-bold text-gray-400 uppercase">
            Mostrando {{ $deployments->firstItem() ?? 0 }}–{{ $deployments->lastItem() ?? 0 }} de {{ $deployments->total() }} operaciones
        </p>
        {{ $deployments->appends(request()->query())->links() }}
    </div>

</div>
@endsection