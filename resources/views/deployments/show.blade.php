@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-tracking { height: 350px; width: 100%; border-radius: 20px; z-index: 1; border: 4px solid white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .dark #map-tracking { border-color: #1e293b; }
    .leaflet-layer, .leaflet-control-zoom-in, .leaflet-control-zoom-out, .leaflet-control-attribution {
        filter: var(--map-filter, none);
    }
    .dark { --map-filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%); }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
@php
    $status = $deployment->computeStatus();
    $statusConfig = [
        'programado' => ['bg' => 'bg-blue-100 text-blue-700',  'border' => 'border-blue-300',  'dot' => 'bg-pc-blue',    'label' => 'Programado', 'hero' => 'from-blue-50 to-indigo-50', 'ping' => false, 'bar' => 'bg-pc-blue'],
        'en_curso'   => ['bg' => 'bg-green-100 text-green-700','border' => 'border-green-300', 'dot' => 'bg-green-500',  'label' => 'En Curso',   'hero' => 'from-green-50 to-emerald-50', 'ping' => true, 'bar' => 'bg-green-500'],
        'finalizado' => ['bg' => 'bg-gray-100 text-gray-600',  'border' => 'border-gray-300',  'dot' => 'bg-gray-400',   'label' => 'Finalizado', 'hero' => 'from-gray-50 to-slate-50', 'ping' => false, 'bar' => 'bg-gray-400'],
        'cancelado'  => ['bg' => 'bg-red-100 text-red-700',    'border' => 'border-red-300',   'dot' => 'bg-pc-red',     'label' => 'Cancelado',  'hero' => 'from-red-50 to-rose-50', 'ping' => false, 'bar' => 'bg-pc-red'],
    ];
    $sc = $statusConfig[$status] ?? $statusConfig['programado'];

    // Progress calculation
    $start = $deployment->start_datetime;
    $end   = $deployment->end_datetime;
    $showProgress = !$deployment->is_indefinite && $end && in_array($status, ['en_curso', 'finalizado']);
    $progress = 0;
    if ($showProgress) {
        $totalMinutes = max(1, $start->diffInMinutes($end));
        $elapsedMinutes = max(0, min($start->diffInMinutes(now()), $totalMinutes));
        $progress = (int) round(($elapsedMinutes / $totalMinutes) * 100);
    }
@endphp

<div class="py-8 max-w-7xl mx-auto space-y-6">

    {{-- HERO HEADER --}}
    <div class="card-pc overflow-hidden dark:bg-slate-900 dark:border-slate-800">
        {{-- Banner de color --}}
        <div class="h-2 w-full
            {{ $status === 'en_curso'   ? 'bg-gradient-to-r from-green-400 to-emerald-500' :
               ($status === 'programado'? 'bg-gradient-to-r from-blue-400 to-indigo-500' :
               ($status === 'cancelado' ? 'bg-gradient-to-r from-red-500 to-rose-500' :
                                          'bg-gradient-to-r from-gray-300 to-slate-400')) }}">
        </div>

        <div class="p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <a href="{{ route('deployments.index') }}" class="w-8 h-8 flex items-center justify-center bg-gray-50 dark:bg-slate-800 text-gray-400 hover:text-pc-blue rounded-xl border border-gray-100 dark:border-slate-700 transition-all shadow-sm">
                            <i class="fas fa-arrow-left text-xs"></i>
                        </a>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 text-[9px] font-black rounded-xl uppercase tracking-widest border {{ $sc['bg'] }} {{ $sc['border'] }}">
                            @if($sc['ping'])
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $sc['dot'] }} opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $sc['dot'] }}"></span>
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full {{ $sc['dot'] }}"></span>
                            @endif
                            {{ $sc['label'] }}
                        </div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Operación #{{ $deployment->id }}
                        </span>
                    </div>
                    <h2 class="text-3xl font-black text-pc-blue dark:text-white uppercase tracking-tight">
                        <i class="fas fa-location-dot mr-2 text-pc-orange/50 text-2xl"></i>{{ $deployment->place }}
                    </h2>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-2">
                        División: {{ $deployment->division ?: 'Sin asignar' }}
                        @if($deployment->reason)
                            &nbsp;·&nbsp;{{ $deployment->reason }}
                        @endif
                    </p>
                </div>

                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('deployments.pdf', $deployment) }}"
                       class="bg-pc-red hover:bg-red-700 text-white font-black text-[10px] uppercase px-5 py-3 rounded-xl shadow-lg shadow-red-100 transition-all flex items-center gap-2">
                        <i class="fas fa-file-pdf"></i> Reporte
                    </a>
                    @can('update', $deployment)
                        <a href="{{ route('deployments.edit', $deployment) }}"
                           class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 hover:bg-indigo-500 hover:text-white font-black text-[10px] uppercase px-5 py-3 rounded-xl border border-indigo-100 dark:border-indigo-500/20 transition-all shadow-sm flex items-center gap-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Barra de Progreso y Timeline --}}
            @if($showProgress || $status === 'programado' || $deployment->is_indefinite)
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-3 text-[9px] font-black uppercase text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-start">
                        <span class="text-pc-blue font-black">INICIO</span>
                        <span class="text-gray-700 dark:text-gray-200 text-[11px] font-black mt-0.5">{{ $start->format('d M Y H:i') }}</span>
                    </div>
                    @if($status === 'en_curso' && $showProgress)
                        <div class="flex flex-col items-center">
                            <span class="text-green-600 font-black animate-pulse">● AHORA</span>
                        </div>
                    @endif
                    <div class="flex flex-col items-end">
                        <span class="{{ $status === 'finalizado' ? 'text-gray-400' : 'text-gray-400' }} font-black">FIN</span>
                        @if($deployment->is_indefinite)
                            <span class="text-pc-red text-[11px] font-black mt-0.5 italic">INDEFINIDO</span>
                        @else
                            <span class="text-gray-700 dark:text-gray-200 text-[11px] font-black mt-0.5">{{ $end?->format('d M Y H:i') ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>

                @if($showProgress)
                <div class="relative h-4 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                    <div class="h-full {{ $sc['bar'] }} rounded-full transition-all duration-700 ease-out flex items-center justify-end pr-2"
                         style="width: {{ max(4, $progress) }}%">
                        @if($progress > 15)
                            <span class="text-white text-[8px] font-black">{{ $progress }}%</span>
                        @endif
                    </div>
                </div>
                @elseif($deployment->is_indefinite && $status === 'en_curso')
                <div class="relative h-4 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                    <div class="h-full bg-green-500 rounded-full w-full opacity-50 bg-stripe-pattern animate-progress"></div>
                </div>
                <style>
                    .bg-stripe-pattern { background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent); background-size: 1rem 1rem; }
                    @keyframes progress { from { background-position: 1rem 0; } to { background-position: 0 0; } }
                    .animate-progress { animation: progress 1s linear infinite; }
                </style>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna Izquierda (Mapa y Detalles) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Visualización Táctica (Mapa) --}}
            <div class="card-pc border-t-4 border-pc-blue p-2 dark:bg-slate-900 dark:border-slate-800 shadow-lg">
                @if($deployment->latitude && $deployment->longitude)
                    <div id="map-tracking" class="skeleton"></div>
                @else
                    <div class="h-[350px] bg-gray-50 dark:bg-slate-800/50 rounded-[20px] flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-200 dark:border-slate-700">
                        <i class="fas fa-map-marked-alt text-5xl mb-4 opacity-20"></i>
                        <p class="text-xs font-black uppercase tracking-widest">Sin geolocalización registrada</p>
                    </div>
                @endif
            </div>

            {{-- Notas Técnicas --}}
            @if($deployment->notes)
            <div class="card-pc p-6 dark:bg-slate-900 dark:border-slate-800 border-l-4 border-pc-orange">
                <h3 class="text-[10px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i class="fas fa-align-left text-pc-orange"></i> Notas Técnicas
                </h3>
                <p class="text-xs font-bold text-gray-600 dark:text-gray-400 leading-relaxed bg-orange-50 dark:bg-orange-500/5 p-4 rounded-xl border border-orange-100 dark:border-orange-500/10">
                    {!! nl2br(e($deployment->notes)) !!}
                </p>
            </div>
            @endif
        </div>

        {{-- Columna Derecha (Logística y Participantes) --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Info Logística --}}
            <div class="card-pc p-6 border-t-4 border-pc-blue dark:bg-slate-900 dark:border-slate-800">
                <h3 class="text-[10px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-pc-orange"></i> Mando y Logística
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between border-b border-gray-50 dark:border-slate-700 pb-3">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Supervisor</span>
                        <span class="text-xs font-black text-gray-800 dark:text-gray-200 text-right max-w-[60%]">
                            {{ $deployment->supervisor?->full_name ?? 'Sin asignar' }}
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 dark:border-slate-700 pb-3">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Personal</span>
                        <span class="text-xs font-black text-gray-800 dark:text-gray-200">
                            {{ $deployment->participants->count() }} Efectivos
                        </span>
                    </div>
                </div>
            </div>

            {{-- Participantes --}}
            <div class="card-pc p-6 border-t-4 border-pc-blue dark:bg-slate-900 dark:border-slate-800">
                <h3 class="text-[10px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-pc-blue"></i> Fuerza Desplegada
                </h3>
                <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($deployment->participants as $p)
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30">
                            <div class="w-8 h-8 rounded-lg bg-pc-blue/10 text-pc-blue flex items-center justify-center text-[10px] font-black shrink-0">
                                {{ strtoupper(substr($p->first_name, 0, 1) . substr($p->last_name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-black text-gray-800 dark:text-gray-200 truncate">{{ $p->full_name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[8px] font-bold text-pc-orange uppercase">{{ $p->pivot->role ?: 'Operador' }}</span>
                                    @if($p->pivot->is_leader)
                                        <span class="text-[7px] font-black bg-pc-orange text-white px-1.5 py-0.5 rounded uppercase">Líder</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($deployment->participants->isEmpty())
                        <div class="text-center py-6">
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Sin personal asignado</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Acciones Finales --}}
            @if(in_array($status, ['programado', 'en_curso']))
            <div class="card-pc p-6 dark:bg-slate-900 dark:border-slate-800 border-t-4 border-pc-blue">
                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4 text-center">
                    Acciones de Misión
                </h3>
                <div class="space-y-3">
                    <form action="{{ route('deployments.change-status', $deployment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="finalizado">
                        <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-black text-[10px] uppercase py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                            <i class="fas fa-flag-checkered"></i> Concluir Operación
                        </button>
                    </form>
                    <form action="{{ route('deployments.change-status', $deployment) }}" method="POST" class="confirm-delete" data-label="cancelar esta misión">
                        @csrf
                        <input type="hidden" name="status" value="cancelado">
                        <button type="submit" class="w-full bg-white dark:bg-slate-800 border border-pc-red/20 text-pc-red font-black text-[10px] uppercase py-3.5 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-ban"></i> Abortar Operación
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($deployment->latitude && $deployment->longitude)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('map-tracking').setView([{{ $deployment->latitude }}, {{ $deployment->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const iconStatus = L.divIcon({
            className: '',
            html: '<div style="width:16px;height:16px;background:{{ $sc['dot'] === 'bg-green-500' ? '#22c55e' : ($sc['dot'] === 'bg-pc-blue' ? '#1e40af' : '#9ca3af') }};border:3px solid white;border-radius:50%;box-shadow:0 0 0 4px rgba(0,0,0,0.1)"></div>',
            iconSize: [16, 16], iconAnchor: [8, 8]
        });

        const marker = L.marker([{{ $deployment->latitude }}, {{ $deployment->longitude }}], { icon: iconStatus }).addTo(map);
        marker.bindPopup(`
            <div class="text-center p-2">
                <p class="text-[10px] font-black text-pc-blue uppercase">{{ $deployment->place }}</p>
                <p class="text-[8px] font-bold text-gray-500 uppercase mt-1">Punto de Operación</p>
            </div>
        `).openPopup();

        document.getElementById('map-tracking').classList.remove('skeleton');
    });
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #334155; }
</style>
@endpush
@endif
@endsection