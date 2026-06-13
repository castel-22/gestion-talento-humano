@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 dark:text-gray-400 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <a href="{{ route('leaves.index') }}" class="text-sm text-gray-700 dark:text-gray-400 hover:text-pc-orange">Reposos Médicos</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Detalle del Reposo</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
@php
    $computed  = $leave->computeStatus();
    $progress  = $leave->progress_percent;
    $statusConfig = [
        'pendiente'  => ['bg' => 'bg-yellow-100 text-yellow-700',  'border' => 'border-yellow-300',  'dot' => 'bg-yellow-400',  'label' => 'Pendiente de Aprobación', 'icon' => 'fa-clock',        'ping' => false, 'hero' => 'from-yellow-50 to-amber-50'],
        'aprobado'   => ['bg' => 'bg-blue-100 text-blue-700',       'border' => 'border-blue-300',    'dot' => 'bg-blue-400',    'label' => 'Aprobado',                'icon' => 'fa-check-circle', 'ping' => false, 'hero' => 'from-blue-50 to-indigo-50'],
        'en_curso'   => ['bg' => 'bg-green-100 text-green-700',     'border' => 'border-green-300',   'dot' => 'bg-green-500',   'label' => 'En Curso',                'icon' => 'fa-heartbeat',    'ping' => true,  'hero' => 'from-green-50 to-emerald-50'],
        'finalizado' => ['bg' => 'bg-gray-100 text-gray-600',       'border' => 'border-gray-300',    'dot' => 'bg-gray-400',    'label' => 'Finalizado',              'icon' => 'fa-check-double', 'ping' => false, 'hero' => 'from-gray-50 to-slate-50'],
        'rechazado'  => ['bg' => 'bg-red-100 text-red-700',         'border' => 'border-red-300',     'dot' => 'bg-pc-red',      'label' => 'Rechazado',               'icon' => 'fa-times-circle', 'ping' => false, 'hero' => 'from-red-50 to-rose-50'],
    ];
    $sc = $statusConfig[$computed] ?? $statusConfig['pendiente'];
    $showProgress = in_array($computed, ['aprobado', 'en_curso', 'finalizado']);
    $barColor = $computed === 'en_curso' ? 'bg-green-500' : ($computed === 'finalizado' ? 'bg-blue-400' : 'bg-blue-300');
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ══════════════════════════════════════════════════════
         HERO HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="card-pc overflow-hidden dark:bg-slate-900 dark:border-slate-800">
        {{-- Banner de color según estado --}}
        <div class="h-2 w-full
            {{ $computed === 'en_curso'   ? 'bg-gradient-to-r from-green-400 to-emerald-500' :
               ($computed === 'pendiente'  ? 'bg-gradient-to-r from-yellow-400 to-amber-400' :
               ($computed === 'rechazado'  ? 'bg-gradient-to-r from-red-500 to-rose-500' :
               ($computed === 'finalizado' ? 'bg-gradient-to-r from-gray-300 to-slate-400' :
                                            'bg-gradient-to-r from-blue-400 to-indigo-500'))) }}">
        </div>

        <div class="p-8">
            <div class="flex flex-col md:flex-row md:items-start gap-6">

                {{-- Avatar del empleado --}}
                <div class="w-20 h-20 rounded-2xl bg-pc-red/10 dark:bg-pc-red/20 text-pc-red flex items-center justify-center font-black text-2xl shrink-0 border-2 border-pc-red/20 shadow-sm">
                    {{ strtoupper(substr($leave->employee->first_name, 0, 1) . substr($leave->employee->last_name, 0, 1)) }}
                </div>

                {{-- Info principal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        {{-- Badge estado dinámico --}}
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
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                            Reposo #{{ $leave->id }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight truncate">
                        {{ $leave->employee->full_name }}
                    </h2>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                        <i class="fas fa-id-card mr-1.5"></i>{{ $leave->employee->id_number }}
                        @if($leave->employee->position)
                            &nbsp;·&nbsp;<i class="fas fa-briefcase mr-1"></i>{{ $leave->employee->position }}
                        @endif
                    </p>
                </div>

                {{-- Acciones header --}}
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('leaves.index') }}"
                       class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-slate-800 text-gray-400 hover:text-pc-blue rounded-xl border border-gray-100 dark:border-slate-700 transition-all shadow-sm"
                       title="Volver al listado">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    @can('update', $leave)
                        <a href="{{ route('leaves.edit', $leave) }}"
                           class="w-10 h-10 flex items-center justify-center bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 hover:bg-indigo-500 hover:text-white rounded-xl border border-indigo-100 dark:border-indigo-500/20 transition-all shadow-sm"
                           title="Editar reposo">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                    @endcan
                </div>
            </div>

            {{-- ── Barra de progreso grande ─────────────────── --}}
            @if($showProgress)
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
                {{-- Línea de tiempo: inicio → hoy → fin --}}
                <div class="flex items-center justify-between mb-3 text-[9px] font-black uppercase text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-start">
                        <span class="text-pc-red font-black">INICIO</span>
                        <span class="text-gray-700 dark:text-gray-200 text-[11px] font-black mt-0.5">{{ $leave->start_date->format('d M Y') }}</span>
                    </div>
                    @if($computed === 'en_curso')
                        <div class="flex flex-col items-center">
                            <span class="text-green-600 font-black animate-pulse">● HOY</span>
                            <span class="text-gray-700 dark:text-gray-200 text-[11px] font-black mt-0.5">{{ now()->format('d M Y') }}</span>
                        </div>
                    @endif
                    <div class="flex flex-col items-end">
                        <span class="{{ $computed === 'finalizado' ? 'text-blue-400' : 'text-gray-400' }} font-black">FIN</span>
                        <span class="text-gray-700 dark:text-gray-200 text-[11px] font-black mt-0.5">{{ $leave->end_date->format('d M Y') }}</span>
                    </div>
                </div>

                {{-- Barra --}}
                <div class="relative h-4 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                    <div class="h-full {{ $barColor }} rounded-full transition-all duration-700 ease-out flex items-center justify-end pr-2"
                         style="width: {{ max(4, $progress) }}%">
                        @if($progress > 15)
                            <span class="text-white text-[8px] font-black">{{ $progress }}%</span>
                        @endif
                    </div>
                </div>

                {{-- Stats debajo de la barra --}}
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="text-center bg-gray-50 dark:bg-slate-800 rounded-xl p-3 border border-gray-100 dark:border-slate-700">
                        <p class="text-xl font-black text-pc-blue dark:text-white">{{ $leave->days_elapsed }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Días transcurridos</p>
                    </div>
                    <div class="text-center bg-{{ $computed === 'en_curso' ? 'green' : 'gray' }}-50 dark:bg-slate-800 rounded-xl p-3 border border-{{ $computed === 'en_curso' ? 'green' : 'gray' }}-100 dark:border-slate-700">
                        <p class="text-xl font-black {{ $computed === 'en_curso' ? 'text-green-600' : 'text-gray-400' }}">{{ $leave->days_remaining }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Días restantes</p>
                    </div>
                    <div class="text-center bg-gray-50 dark:bg-slate-800 rounded-xl p-3 border border-gray-100 dark:border-slate-700">
                        <p class="text-xl font-black text-gray-700 dark:text-gray-300">{{ $leave->total_days }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Duración total</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         GRID DE DETALLES
    ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Datos Médicos --}}
        <div class="card-pc p-6 dark:bg-slate-900 dark:border-slate-800">
            <h3 class="text-[10px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest mb-5 flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-pc-red/10 flex items-center justify-center">
                    <i class="fas fa-stethoscope text-pc-red text-[10px]"></i>
                </div>
                Información Médica
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-start border-b border-gray-50 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Médico Tratante</span>
                    <span class="text-xs font-black text-gray-800 dark:text-gray-200 text-right max-w-[55%]">{{ $leave->doctor_name }}</span>
                </div>
                <div class="flex justify-between items-start border-b border-gray-50 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Institución</span>
                    <span class="text-xs font-black text-gray-800 dark:text-gray-200 text-right max-w-[55%]">{{ $leave->issuing_institution }}</span>
                </div>
                @if($leave->medical_condition)
                <div class="pt-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase block mb-2">Diagnóstico / Padecimiento</span>
                    <div class="bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/10 rounded-xl p-3">
                        <p class="text-[11px] font-bold text-gray-700 dark:text-gray-300 leading-relaxed">
                            <i class="fas fa-notes-medical text-pc-red mr-2"></i>{{ $leave->medical_condition }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Datos del Reposo --}}
        <div class="card-pc p-6 dark:bg-slate-900 dark:border-slate-800">
            <h3 class="text-[10px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest mb-5 flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-500 text-[10px]"></i>
                </div>
                Datos del Reposo
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between border-b border-gray-50 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Inicio</span>
                    <span class="text-xs font-black text-gray-800 dark:text-gray-200">{{ $leave->start_date->format('d \d\e F, Y') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-50 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Fin</span>
                    <span class="text-xs font-black text-gray-800 dark:text-gray-200">{{ $leave->end_date->format('d \d\e F, Y') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-50 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Duración</span>
                    <span class="text-xs font-black text-gray-800 dark:text-gray-200">
                        {{ $leave->duration_value }} {{ $leave->duration_label }}
                        <span class="text-gray-400 font-bold">({{ $leave->total_days }} días)</span>
                    </span>
                </div>
                <div class="flex justify-between border-b border-gray-50 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Registrado</span>
                    <span class="text-xs font-black text-gray-800 dark:text-gray-200">{{ $leave->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($leave->approved_by)
                    <div class="flex justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Aprobado por</span>
                        <span class="text-xs font-black text-gray-800 dark:text-gray-200">{{ $leave->approver?->name ?? 'N/A' }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         ACCIONES DE APROBACIÓN
    ══════════════════════════════════════════════════════ --}}
    @can('update', $leave)
        @if($leave->status === 'pendiente')
        <div class="card-pc p-6 dark:bg-slate-900 dark:border-slate-800 border-l-4 border-yellow-400">
            <h3 class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-gavel"></i> Decisión Requerida
            </h3>
            <p class="text-[10px] font-bold text-gray-500 mb-5">
                Este reposo está pendiente de revisión. Por favor, toma una decisión sobre la solicitud.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-black text-[10px] uppercase py-4 rounded-xl shadow-lg shadow-green-100 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> Aprobar Reposo
                    </button>
                </form>
                <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full bg-white dark:bg-slate-800 border border-pc-red/20 text-pc-red font-black text-[10px] uppercase py-4 rounded-xl hover:bg-pc-red/5 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-times-circle"></i> Rechazar Reposo
                    </button>
                </form>
            </div>
        </div>
        @endif
    @endcan

</div>
@endsection