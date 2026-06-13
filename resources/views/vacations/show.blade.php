@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <a href="{{ route('vacations.index') }}" class="text-sm text-gray-700 hover:text-pc-orange">Vacaciones</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Detalle #{{ $vacation->id }}</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
@php
    $employee = $vacation->employee;
    $regDays = max(0, $vacation->days_taken - ($vacation->accumulated_days_used ?? 0));
    $accDays = $vacation->accumulated_days_used ?? 0;
    $isPaused = $vacation->status === \App\Models\Vacation::STATUS_INTERRUMPIDO;
    $isResumed = $vacation->status === \App\Models\Vacation::STATUS_REANUDADO;
    $isActive = in_array($vacation->status, [\App\Models\Vacation::STATUS_APROBADO, \App\Models\Vacation::STATUS_EN_CURSO]);

    // Calculate elapsed/remaining for active or interrupted vacations
    $startDate = \Carbon\Carbon::parse($vacation->start_date);
    $endDate = \Carbon\Carbon::parse($vacation->end_date);
    $today = \Carbon\Carbon::today();
    $totalCalendarDays = $startDate->diffInDays($endDate) + 1;

    $elapsed = 0;
    if ($today->gte($startDate)) {
        $elapsed = min($startDate->diffInDays($today), $vacation->days_taken);
    }

    $progressPercent = $vacation->days_taken > 0 ? round(($elapsed / $vacation->days_taken) * 100) : 0;
    if ($vacation->status === \App\Models\Vacation::STATUS_FINALIZADO) {
        $progressPercent = 100;
    }
@endphp

<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header Card --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 overflow-hidden border border-gray-100">
            <div class="relative">
                {{-- Decorative top bar --}}
                <div class="h-2 w-full bg-gradient-to-r from-pc-blue via-pc-orange to-pc-blue"></div>

                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6">
                        {{-- Employee info --}}
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-pc-blue to-blue-800 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg shadow-blue-200">
                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-gray-900 uppercase">{{ $employee->full_name }}</h2>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-1">
                                    {{ $employee->id_number }} • {{ $employee->department->name ?? 'Sin depto.' }} • {{ $employee->position ?: 'Sin cargo' }}
                                </p>
                            </div>
                        </div>

                        {{-- Status badge + Actions --}}
                        <div class="flex items-center gap-3">
                            @include('partials.vacation-status-badge', ['status' => $vacation->status])
                            <a href="{{ route('vacations.index') }}" class="text-[10px] font-black text-gray-400 uppercase hover:text-pc-blue transition flex items-center gap-1">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Info Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Period Card --}}
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 p-6">
                <h4 class="text-[10px] font-black text-pc-blue uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-pc-orange"></i> Período de Disfrute
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Inicio</span>
                        <span class="text-sm font-black text-gray-800">{{ $vacation->start_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Fin</span>
                        <span class="text-sm font-black text-gray-800">{{ $vacation->end_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Duración</span>
                        <span class="text-sm font-black text-pc-blue">{{ $totalCalendarDays }} días calendario</span>
                    </div>
                </div>
            </div>

            {{-- Days Breakdown Card --}}
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 p-6">
                <h4 class="text-[10px] font-black text-pc-blue uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-layer-group text-pc-orange"></i> Desglose de Días
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Días Regulares</span>
                        <span class="bg-blue-50 text-pc-blue text-sm font-black px-3 py-1 rounded-lg border border-blue-100">{{ $regDays }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Días Acumulados</span>
                        <span class="bg-orange-50 text-pc-orange text-sm font-black px-3 py-1 rounded-lg border border-orange-100">{{ $accDays }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-500 uppercase">Total Solicitado</span>
                        <span class="bg-pc-blue text-white text-sm font-black px-3 py-1 rounded-lg">{{ $vacation->days_taken }}</span>
                    </div>
                </div>
            </div>

            {{-- Administrative Info Card --}}
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 p-6">
                <h4 class="text-[10px] font-black text-pc-blue uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-pc-orange"></i> Información Administrativa
                </h4>
                <div class="space-y-3">
                    @if($vacation->approved_by)
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Aprobado por</span>
                        <span class="text-sm font-bold text-gray-700">{{ $vacation->approver->name ?? 'N/A' }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Creado</span>
                        <span class="text-sm font-bold text-gray-700">{{ $vacation->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Actualizado</span>
                        <span class="text-sm font-bold text-gray-700">{{ $vacation->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($vacation->remaining_days !== null && $vacation->remaining_days > 0)
                    <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-[10px] font-black text-orange-600 uppercase">Días Restantes</span>
                        <span class="bg-orange-100 text-orange-700 text-sm font-black px-3 py-1 rounded-lg border border-orange-200 inline-flex items-center gap-1">
                            <i class="fas fa-hourglass-half text-[8px] animate-pulse"></i>
                            {{ $vacation->remaining_days }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Progress Bar (for active/paused vacations) --}}
        @if(in_array($vacation->status, [\App\Models\Vacation::STATUS_APROBADO, \App\Models\Vacation::STATUS_EN_CURSO, \App\Models\Vacation::STATUS_INTERRUMPIDO, \App\Models\Vacation::STATUS_FINALIZADO]))
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 p-6">
            <h4 class="text-[10px] font-black text-pc-blue uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-pc-orange"></i> Progreso de Disfrute
            </h4>
            <div class="relative">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $vacation->start_date->format('d/m') }}</span>
                    <span class="text-sm font-black text-pc-blue">{{ $progressPercent }}%</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $vacation->end_date->format('d/m') }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-1000 {{ $isPaused ? 'bg-gradient-to-r from-orange-400 to-orange-500' : ($vacation->status === \App\Models\Vacation::STATUS_FINALIZADO ? 'bg-gradient-to-r from-gray-400 to-gray-500' : 'bg-gradient-to-r from-pc-blue to-blue-600') }}"
                         style="width: {{ $progressPercent }}%"></div>
                </div>
                @if($isPaused)
                    <div class="flex items-center gap-2 mt-3 text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-2 rounded-lg border border-orange-100">
                        <i class="fas fa-pause-circle animate-pulse"></i>
                        Vacaciones pausadas — {{ $vacation->remaining_days }} días pendientes de reanudar
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Interruption Details (only if interrupted/resumed) --}}
        @if($vacation->interruption_reason)
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-100 border border-orange-100 p-6">
            <h4 class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-orange-500"></i> Detalle de Interrupción
            </h4>
            <div class="bg-orange-50/50 rounded-xl p-4 border border-orange-100">
                <p class="text-sm text-gray-700 leading-relaxed">{{ $vacation->interruption_reason }}</p>
            </div>
            @if($isPaused && $vacation->remaining_days > 0)
                <div class="mt-4 flex items-center justify-between bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-4 border border-orange-200">
                    <div>
                        <p class="text-[10px] font-black text-orange-700 uppercase tracking-wider">Días por retomar</p>
                        <p class="text-2xl font-black text-orange-600 mt-1">{{ $vacation->remaining_days }}</p>
                    </div>
                    @if($vacation->canBeResumed())
                        <a href="{{ route('vacations.resume.form', $vacation) }}" class="bg-gradient-to-r from-teal-500 to-teal-600 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-teal-200 hover:shadow-xl transition-all flex items-center gap-2">
                            <i class="fas fa-play"></i> Reanudar Vacaciones
                        </a>
                    @else
                        <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-4 py-2 rounded-lg">
                            <i class="fas fa-clock mr-1"></i> Ventana de reanudación expirada (72h)
                        </span>
                    @endif
                </div>
            @endif
        </div>
        @endif

        {{-- Timeline / History --}}
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 p-6">
            <h4 class="text-[10px] font-black text-pc-blue uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-stream text-pc-orange"></i> Línea de Tiempo
            </h4>
            <div class="relative">
                {{-- Vertical line --}}
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-100"></div>

                <div class="space-y-6">
                    {{-- Created --}}
                    <div class="relative flex items-start gap-4 pl-10">
                        <div class="absolute left-2 w-5 h-5 bg-blue-100 border-2 border-pc-blue rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-[7px] text-pc-blue"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-pc-blue uppercase">Solicitud Creada</p>
                            <p class="text-[10px] text-gray-400 font-bold">{{ $vacation->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                        </div>
                    </div>

                    {{-- Approved --}}
                    @if($vacation->approved_by)
                    <div class="relative flex items-start gap-4 pl-10">
                        <div class="absolute left-2 w-5 h-5 bg-emerald-100 border-2 border-emerald-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-[7px] text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-emerald-600 uppercase">Aprobada por {{ $vacation->approver->name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-gray-400 font-bold">Período: {{ $vacation->start_date->format('d/m/Y') }} — {{ $vacation->end_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Started --}}
                    @if($today->gte($startDate) && $vacation->approved_by)
                    <div class="relative flex items-start gap-4 pl-10">
                        <div class="absolute left-2 w-5 h-5 bg-blue-100 border-2 border-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-plane-departure text-[7px] text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase">Inicio del Período</p>
                            <p class="text-[10px] text-gray-400 font-bold">{{ $vacation->start_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Interrupted --}}
                    @if($isPaused || $isResumed)
                    <div class="relative flex items-start gap-4 pl-10">
                        <div class="absolute left-2 w-5 h-5 bg-orange-100 border-2 border-orange-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-pause text-[7px] text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-orange-600 uppercase">Vacaciones Interrumpidas</p>
                            <p class="text-[10px] text-gray-400 font-bold">{{ $vacation->updated_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                            @if($vacation->interruption_reason)
                                <p class="text-[10px] text-orange-500 font-medium mt-1 italic">"{{ Str::limit($vacation->interruption_reason, 80) }}"</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Resumed --}}
                    @if($isResumed)
                    <div class="relative flex items-start gap-4 pl-10">
                        <div class="absolute left-2 w-5 h-5 bg-teal-100 border-2 border-teal-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-redo text-[7px] text-teal-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-teal-600 uppercase">Días Reanudados (nueva solicitud creada)</p>
                            <p class="text-[10px] text-gray-400 font-bold">Todos los días restantes fueron reprogramados</p>
                        </div>
                    </div>
                    @endif

                    {{-- Finalized --}}
                    @if($vacation->status === \App\Models\Vacation::STATUS_FINALIZADO)
                    <div class="relative flex items-start gap-4 pl-10">
                        <div class="absolute left-2 w-5 h-5 bg-gray-200 border-2 border-gray-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-flag-checkered text-[7px] text-gray-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-600 uppercase">Vacaciones Finalizadas</p>
                            <p class="text-[10px] text-gray-400 font-bold">{{ $vacation->end_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Rejected --}}
                    @if($vacation->status === \App\Models\Vacation::STATUS_RECHAZADO)
                    <div class="relative flex items-start gap-4 pl-10">
                        <div class="absolute left-2 w-5 h-5 bg-red-100 border-2 border-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-[7px] text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-red-600 uppercase">Solicitud Rechazada</p>
                            <p class="text-[10px] text-gray-400 font-bold">{{ $vacation->updated_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 p-6">
            <div class="flex flex-wrap gap-3 justify-end">
                @if($vacation->status === \App\Models\Vacation::STATUS_PENDIENTE)
                    <form action="{{ route('vacations.approve', $vacation) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-emerald-200 hover:shadow-xl transition-all flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Aprobar Solicitud
                        </button>
                    </form>
                    <form action="{{ route('vacations.reject', $vacation) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-red-200 hover:shadow-xl transition-all flex items-center gap-2">
                            <i class="fas fa-times-circle"></i> Rechazar
                        </button>
                    </form>
                    @can('update', $vacation)
                        <a href="{{ route('vacations.edit', $vacation) }}" class="bg-white border border-gray-200 text-gray-600 font-black text-[10px] uppercase px-6 py-3 rounded-xl hover:bg-gray-50 transition-all flex items-center gap-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endcan
                @endif

                @if($vacation->canBeInterrupted())
                    <button type="button" onclick="interruptVacation()" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-orange-200 hover:shadow-xl transition-all flex items-center gap-2">
                        <i class="fas fa-pause-circle"></i> Interrumpir
                    </button>
                @endif

                @if($vacation->canBeResumed())
                    <a href="{{ route('vacations.resume.form', $vacation) }}" class="bg-gradient-to-r from-teal-500 to-teal-600 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-teal-200 hover:shadow-xl transition-all flex items-center gap-2">
                        <i class="fas fa-play"></i> Reanudar
                    </a>
                @endif

                @if(in_array($vacation->status, [\App\Models\Vacation::STATUS_APROBADO, \App\Models\Vacation::STATUS_EN_CURSO]))
                    <form action="{{ route('vacations.finalize', $vacation) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-white border border-gray-200 text-gray-600 font-black text-[10px] uppercase px-6 py-3 rounded-xl hover:bg-gray-50 transition-all flex items-center gap-2">
                            <i class="fas fa-flag-checkered"></i> Finalizar
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</div>

@if($vacation->canBeInterrupted())
<form id="interrupt-form" method="POST" action="{{ route('vacations.interrupt', $vacation) }}" style="display: none;">
    @csrf
    <input type="hidden" name="interruption_reason" id="reason-input">
</form>
<script>
    function interruptVacation() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Interrumpir Vacaciones',
                input: 'textarea',
                inputLabel: 'Motivo de la interrupción',
                inputPlaceholder: 'Escriba el motivo institucional de la interrupción...',
                inputAttributes: { 'aria-label': 'Motivo de la interrupción' },
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-pause-circle"></i> Confirmar Interrupción',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#F97316',
                inputValidator: (value) => {
                    if (!value) return 'Debe indicar un motivo de interrupción.';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reason-input').value = result.value;
                    document.getElementById('interrupt-form').submit();
                }
            });
        } else {
            const reason = prompt('Motivo de interrupción:');
            if (reason) {
                document.getElementById('reason-input').value = reason;
                document.getElementById('interrupt-form').submit();
            }
        }
    }
</script>
@endif
@endsection