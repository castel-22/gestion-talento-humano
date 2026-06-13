@extends('layouts.app')

@section('content')
<div class="py-6" x-data="resumeForm({{ $vacation->id }}, {{ $vacation->remaining_days }})">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-pc-blue">Reanudar Vacaciones</h2>
            </div>

            {{-- Datos del empleado --}}
            <div class="p-5 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Empleado:</span>
                        <span class="font-medium">{{ $vacation->employee->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Período original:</span>
                        <span class="font-medium">{{ $vacation->start_date->format('d/m/Y') }} - {{ $vacation->end_date->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Días restantes:</span>
                        <span class="font-bold text-pc-blue" x-text="remainingDays"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Nueva solicitud:</span>
                        <span class="text-sm text-gray-600">Se creará con los días que elijas</span>
                    </div>
                </div>
            </div>

            {{-- Notificación --}}
            <div x-show="notification.show" x-cloak class="mx-5 mt-4">
                <div :class="notification.type === 'success' ? 'bg-green-50 border-l-4 border-green-500' : 'bg-red-50 border-l-4 border-red-500'" class="p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i :class="notification.type === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500'"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm" :class="notification.type === 'success' ? 'text-green-700' : 'text-red-700'" x-text="notification.message"></p>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitResume" class="p-5">
                @csrf

                {{-- Días a tomar --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Días a tomar <span class="text-red-500">*</span></label>
                    <input type="number" x-model="daysToTake" @@input="calculateEndDate" required min="1" :max="remainingDays" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange/20">
                    <p class="text-xs text-gray-500 mt-1">Máximo disponible: <span x-text="remainingDays"></span> días</p>
                </div>

                {{-- Fechas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nueva fecha inicio <span class="text-red-500">*</span></label>
                        <input type="date" x-model="startDate" @@change="calculateEndDate" required min="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nueva fecha fin <span class="text-red-500">*</span></label>
                        <input type="date" x-model="endDate" required min="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:border-pc-orange focus:ring focus:ring-pc-orange/20" :readonly="autoCompute">
                        <p class="text-xs text-gray-400 mt-1" x-show="autoCompute">Se calcula automáticamente</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('vacations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">Cancelar</a>
                    <button type="submit" class="bg-pc-blue hover:bg-blue-700 text-white px-5 py-2 rounded-md transition disabled:opacity-50" :disabled="isSubmitting">
                        <span x-show="!isSubmitting"><i class="fas fa-redo mr-1"></i> Reanudar</span>
                        <span x-show="isSubmitting"><i class="fas fa-spinner fa-spin mr-1"></i> Procesando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('resumeForm', (vacationId, remainingDays) => ({
            vacationId: vacationId,
            remainingDays: remainingDays,
            daysToTake: remainingDays,
            startDate: '',
            endDate: '',
            autoCompute: true,
            isSubmitting: false,
            notification: { show: false, type: 'success', message: '' },

            calculateEndDate() {
                if (!this.startDate || !this.daysToTake) {
                    this.endDate = '';
                    return;
                }
                const start = new Date(this.startDate);
                const days = parseInt(this.daysToTake) || 0;
                if (days <= 0) return;
                const end = new Date(start);
                end.setDate(start.getDate() + days);
                this.endDate = end.toISOString().split('T')[0];
            },

            async submitResume() {
                if (!this.startDate || !this.endDate) {
                    this.showNotification('Seleccione ambas fechas.', 'error');
                    return;
                }
                if (this.daysToTake < 1 || this.daysToTake > this.remainingDays) {
                    this.showNotification('Días inválidos.', 'error');
                    return;
                }

                this.isSubmitting = true;
                this.notification.show = false;

                try {
                    const response = await fetch(`{{ url('/vacations') }}/${this.vacationId}/resume`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            start_date: this.startDate,
                            end_date: this.endDate,
                            days_to_take: this.daysToTake
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.showNotification(data.message || 'Reanudación exitosa.', 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("vacations.index") }}';
                        }, 1000);
                    } else {
                        this.showNotification(data.error || 'Error al reanudar.', 'error');
                        this.isSubmitting = false;
                    }
                } catch (e) {
                    console.error(e);
                    this.showNotification('Error de conexión.', 'error');
                    this.isSubmitting = false;
                }
            },

            showNotification(message, type) {
                this.notification = { show: true, type: type, message: message };
                setTimeout(() => { this.notification.show = false; }, 5000);
            }
        }));
    });
</script>
@endpush