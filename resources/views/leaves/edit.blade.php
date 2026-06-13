@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <h2 class="text-2xl font-bold mb-4">Editar Reposo</h2>
        <form method="POST" action="{{ route('leaves.update', $leave) }}" id="leaveForm">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Empleado</label>
                <select name="employee_id" class="border rounded w-full py-2 px-3" required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id', $leave->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
                @error('employee_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de inicio</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" class="border rounded w-full py-2 px-3" required>
                @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Duración (cantidad)</label>
                    <input type="number" name="duration_value" id="duration_value" value="{{ old('duration_value', $leave->duration_value) }}" min="1" class="border rounded w-full py-2 px-3" required>
                    @error('duration_value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Unidad</label>
                    <select name="duration_unit" id="duration_unit" class="border rounded w-full py-2 px-3" required>
                        <option value="days" {{ $leave->duration_unit == 'days' ? 'selected' : '' }}>Días</option>
                        <option value="weeks" {{ $leave->duration_unit == 'weeks' ? 'selected' : '' }}>Semanas</option>
                        <option value="months" {{ $leave->duration_unit == 'months' ? 'selected' : '' }}>Meses</option>
                    </select>
                    @error('duration_unit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de fin (calculada)</label>
                <input type="text" id="end_date_display" class="border rounded w-full py-2 px-3 bg-gray-100" readonly>
                <input type="hidden" name="end_date" id="end_date">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Médico que emite</label>
                <input type="text" name="doctor_name" value="{{ old('doctor_name', $leave->doctor_name) }}" class="border rounded w-full py-2 px-3" required>
                @error('doctor_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Institución emisora</label>
                <input type="text" name="issuing_institution" value="{{ old('issuing_institution', $leave->issuing_institution) }}" class="border rounded w-full py-2 px-3" required>
                @error('issuing_institution') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Padecimiento (opcional)</label>
                <textarea name="medical_condition" rows="3" class="border rounded w-full py-2 px-3">{{ old('medical_condition', $leave->medical_condition) }}</textarea>
                @error('medical_condition') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Estado</label>
                <select name="status" class="border rounded w-full py-2 px-3" required>
                    <option value="pendiente" {{ $leave->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobado" {{ $leave->status == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    <option value="rechazado" {{ $leave->status == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    <option value="finalizado" {{ $leave->status == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                </select>
                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('leaves.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded">Cancelar</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">Actualizar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const startDateInput = document.getElementById('start_date');
    const durationValueInput = document.getElementById('duration_value');
    const durationUnitSelect = document.getElementById('duration_unit');
    const endDateDisplay = document.getElementById('end_date_display');
    const endDateHidden = document.getElementById('end_date');

    function calculateEndDate() {
        let start = startDateInput.value;
        let value = parseInt(durationValueInput.value);
        let unit = durationUnitSelect.value;

        if (!start || !value) {
            endDateDisplay.value = '';
            endDateHidden.value = '';
            return;
        }

        let date = new Date(start);
        if (unit === 'days') {
            date.setDate(date.getDate() + value);
        } else if (unit === 'weeks') {
            date.setDate(date.getDate() + value * 7);
        } else if (unit === 'months') {
            date.setMonth(date.getMonth() + value);
        }
        let year = date.getFullYear();
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let day = String(date.getDate()).padStart(2, '0');
        let formatted = `${year}-${month}-${day}`;
        endDateDisplay.value = formatted;
        endDateHidden.value = formatted;
    }

    calculateEndDate(); // inicializar con valores actuales

    startDateInput.addEventListener('change', calculateEndDate);
    durationValueInput.addEventListener('input', calculateEndDate);
    durationUnitSelect.addEventListener('change', calculateEndDate);
</script>
@endpush
@endsection