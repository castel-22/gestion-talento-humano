@extends('layouts.app')

@section('content')
{{-- Contenedor de datos iniciales para el formulario de edición (sin JavaScript) --}}
@php
    $balance = $vacation->employee->getVacationBalance();
    // Sumamos lo que ya tiene la vacación actual a lo disponible para que el usuario pueda editar sin excederse
    $regUsedByThis = max(0, $vacation->days_taken - ($vacation->accumulated_days_used ?? 0));
    $accUsedByThis = $vacation->accumulated_days_used ?? 0;
    
    $regAvail = $balance['regular_available'] + (in_array($vacation->status, [\App\Models\Vacation::STATUS_APROBADO, \App\Models\Vacation::STATUS_EN_CURSO]) ? $regUsedByThis : 0);
    $accAvail = $balance['accumulated_available'] + (in_array($vacation->status, [\App\Models\Vacation::STATUS_APROBADO, \App\Models\Vacation::STATUS_EN_CURSO]) ? $accUsedByThis : 0);
@endphp

<div id="vacation-edit-data"
     data-available-days="{{ $regAvail + $accAvail }}"
     data-reg-available="{{ $regAvail }}"
     data-acc-available="{{ $accAvail }}"
     data-initial-start-date="{{ old('start_date', $vacation->start_date->format('Y-m-d')) }}"
     data-initial-end-date="{{ old('end_date', $vacation->end_date->format('Y-m-d')) }}"
     data-initial-reg-days="{{ old('regular_days_to_take', $regUsedByThis) }}"
     data-initial-acc-days="{{ old('accumulated_days_to_take', $accUsedByThis) }}"
></div>

<div class="py-6" x-data="editVacationForm()" x-cloak>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-pc-blue">Editar Solicitud de Vacaciones</h2>
            </div>
            <form action="{{ route('vacations.update', $vacation) }}" method="POST" class="p-6" @@submit.prevent="submitForm">
                @csrf
                @method('PUT')

                <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $vacation->employee->full_name }}</h4>
                            <p class="text-xs text-gray-500">{{ $vacation->employee->id_number }} • {{ $vacation->employee->department->name ?? 'Sin depto.' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 bg-pc-blue text-white text-[10px] rounded uppercase font-bold">{{ $vacation->status }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-white p-2 rounded border border-blue-50 text-center">
                            <span class="block text-[10px] text-gray-500 uppercase">Reg. Disponible</span>
                            <span class="text-lg font-bold text-pc-blue" x-text="regAvailable"></span>
                        </div>
                        <div class="bg-white p-2 rounded border border-orange-50 text-center">
                            <span class="block text-[10px] text-gray-500 uppercase text-pc-orange">Acum. Disponible</span>
                            <span class="text-lg font-bold text-pc-orange" x-text="accAvailable"></span>
                        </div>
                        <div class="bg-pc-blue p-2 rounded text-center text-white">
                            <span class="block text-[10px] opacity-80 uppercase">Total</span>
                            <span class="text-lg font-bold" x-text="availableDays"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                        <input type="date" name="start_date" x-model="form.start_date" @@change="calculateEndDate" required
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                        <input type="date" name="end_date" x-model="form.end_date" required
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 bg-gray-50" readonly>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-6">
                    <h4 class="text-xs font-bold text-pc-blue uppercase mb-3">Distribución de Días</h4>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="regular_days_to_take" class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Días Regulares</label>
                            <input type="number" name="regular_days_to_take" x-model="form.reg_days" @@input="updateTotalDays" required min="0" :max="regAvailable"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pc-blue focus:ring focus:ring-pc-blue focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="accumulated_days_to_take" class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Días Acumulados</label>
                            <input type="number" name="accumulated_days_to_take" x-model="form.acc_days" @@input="updateTotalDays" required min="0" :max="accAvailable"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50">
                        </div>
                    </div>
                    <div class="pt-3 border-t border-blue-200 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total:</span>
                        <span class="text-xl font-black text-pc-blue"><span x-text="totalDays"></span> días</span>
                    </div>
                    <input type="hidden" name="days_taken" :value="totalDays">
                    <p x-show="daysError" class="mt-2 text-xs text-red-600 font-bold" x-text="daysError"></p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('vacations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">Cancelar</a>
                    <button type="submit" class="bg-pc-blue hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Cargar el script externo con la lógica Alpine (el archivo JS lee los datos del div #vacation-edit-data) --}}
<script src="{{ asset('js/vacations-edit.js') }}"></script>
@endpush