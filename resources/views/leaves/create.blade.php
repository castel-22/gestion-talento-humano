@extends('layouts.app')

@section('content')
<div class="py-4" x-data="leaveForm()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Encabezado Mejorado --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-pc-blue dark:text-white tracking-tight flex items-center gap-3">
                    <i class="fas fa-notes-medical text-pc-red"></i> Cargar Reposo Médico
                </h1>
                <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-1">
                    Registro y control de ausencias por motivos de salud
                </p>
            </div>
            <a href="{{ route('leaves.index') }}" class="btn-pc-secondary text-xs px-4 py-2 flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al listado
            </a>
        </div>

        <div class="card-pc border-t-4 border-pc-red overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                <i class="fas fa-heart-pulse text-9xl text-pc-red"></i>
            </div>

            {{-- Bloque de notificaciones --}}
            <div x-show="notification.show" x-cloak class="m-5 mb-0">
                <div :class="{
                    'bg-red-50 border-l-4 border-red-500': notification.type === 'error',
                    'bg-green-50 border-l-4 border-green-500': notification.type === 'success'
                }" class="p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i :class="notification.type === 'error' ? 'fas fa-exclamation-circle text-red-500' : 'fas fa-check-circle text-green-500'"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold" :class="notification.type === 'error' ? 'text-red-700' : 'text-green-700'" x-text="notification.message"></p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" @@click="notification.show = false" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('leaves.store') }}" id="leave-form" class="p-5" @@submit.prevent="submitForm">
                @csrf

                {{-- Buscador Autocompletado --}}
                <div class="mb-8 relative z-10">
                    <label class="block text-[10px] font-black text-pc-blue dark:text-gray-300 mb-2 uppercase tracking-widest">
                        Buscar Empleado <span class="text-gray-400 font-bold ml-1">(Cédula o Nombre)</span> <span class="text-pc-red">*</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               x-model="searchTerm"
                               @@input.debounce.300ms="fetchSuggestions"
                               @@keydown.escape="closeSuggestions"
                               @@keydown.arrow-down.prevent="highlightNext"
                               @@keydown.arrow-up.prevent="highlightPrev"
                               @@keydown.enter.prevent="selectHighlighted"
                               @@focus="fetchSuggestions"
                               placeholder="Ej. V-12345678 o Juan Pérez"
                               class="w-full border-gray-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-inner focus:border-pc-orange focus:ring focus:ring-pc-orange/20 pl-11 py-3 text-sm transition-all input-pc"
                               autocomplete="off">
                        <div class="absolute z-10 inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <button type="button" x-show="searchTerm" @@click="clearSearch" class="absolute z-10 inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div x-show="isSearching" class="absolute right-12 top-11">
                        <i class="fas fa-spinner fa-spin text-pc-orange"></i>
                    </div>

                    <div x-show="suggestions.length > 0 && showSuggestions" @@click.away="closeSuggestions" x-transition
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl shadow-2xl max-h-60 overflow-auto overflow-x-hidden divide-y divide-gray-50 dark:divide-slate-700/50">
                        <template x-for="(emp, index) in suggestions" :key="emp.id">
                            <div @@click="selectEmployee(emp)" @@mouseenter="highlightIndex = index"
                                 class="px-5 py-3 cursor-pointer hover:bg-pc-blue/5 dark:hover:bg-slate-700 transition-colors flex items-center justify-between"
                                 :class="{ 'bg-pc-blue/5 dark:bg-slate-700': highlightIndex === index }">
                                <div class="font-black text-gray-800 dark:text-gray-200 text-xs" x-text="emp.label || emp.full_name"></div>
                                <div class="text-[10px] font-bold text-pc-orange uppercase tracking-widest bg-orange-50 dark:bg-orange-500/10 px-2 py-0.5 rounded-full" x-text="emp.value || emp.id_number"></div>
                            </div>
                        </template>
                    </div>
                    <p x-show="searchError" class="mt-1 text-sm text-red-600 font-bold" x-text="searchError"></p>
                </div>

                {{-- Datos del empleado --}}
                <div x-show="employee.id" x-transition.opacity.duration.500ms class="mb-10 p-5 bg-gradient-to-r from-blue-50 to-transparent dark:from-slate-800 dark:to-transparent rounded-2xl border border-blue-100 dark:border-slate-700 shadow-sm relative z-10 flex items-center justify-between">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-white dark:bg-slate-900 border-2 border-pc-blue text-pc-blue rounded-full flex items-center justify-center text-xl shadow-md">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-pc-blue dark:text-pc-orange uppercase tracking-widest mb-0.5">Paciente Asignado</p>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight" x-text="employee.full_name"></h3>
                            <div class="flex items-center gap-3 mt-1 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                <span><i class="fas fa-id-card text-gray-400 mr-1"></i> <span x-text="employee.id_number"></span></span>
                                <span><i class="fas fa-briefcase text-gray-400 mr-1"></i> <span x-text="(employee.department || 'Sin depto.')"></span></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="employee_id" x-model="employee.id">
                </div>

                {{-- Detalles del Reposo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 relative z-10">
                    <div class="group">
                        <label class="label-pc mb-2">Fecha de Inicio <span class="text-pc-red">*</span></label>
                        <div class="relative">
                            <i class="fas fa-calendar-plus absolute left-4 top-3.5 text-pc-blue/50 dark:text-slate-400 group-hover:text-pc-blue transition-colors"></i>
                            <input type="date" name="start_date" x-model="form.start_date" @@change="calculateEndDate" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                        </div>
                    </div>

                    <div class="group">
                        <label class="label-pc mb-2">Fecha de Culminación <span class="text-gray-400 ml-1 font-bold">(Calculada)</span></label>
                        <div class="relative">
                            <i class="fas fa-calendar-check absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="date" x-model="form.end_date" class="input-pc pl-12 bg-gray-100 dark:bg-slate-800/50 text-gray-500 dark:text-gray-400 font-bold border-dashed cursor-not-allowed" readonly>
                            <input type="hidden" name="end_date" :value="form.end_date">
                        </div>
                    </div>

                    <div class="group">
                        <label class="label-pc mb-2">Duración (Cantidad) <span class="text-pc-red">*</span></label>
                        <div class="relative">
                            <i class="fas fa-clock absolute left-4 top-3.5 text-pc-blue/50 dark:text-slate-400 group-hover:text-pc-blue transition-colors"></i>
                            <input type="number" name="duration_value" x-model="form.duration_value" @@input="calculateEndDate" min="1" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                        </div>
                    </div>

                    <div class="group">
                        <label class="label-pc mb-2">Unidad de Tiempo <span class="text-pc-red">*</span></label>
                        <div class="relative">
                            <i class="fas fa-hourglass-half absolute left-4 top-3.5 text-pc-blue/50 dark:text-slate-400 group-hover:text-pc-blue transition-colors"></i>
                            <select name="duration_unit" x-model="form.duration_unit" @@change="calculateEndDate" class="input-pc pl-12 bg-transparent appearance-none dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                <option value="days">Días</option>
                                <option value="weeks">Semanas</option>
                                <option value="months">Meses</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-4 text-xs text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="md:col-span-2 group">
                        <label class="label-pc mb-2">Médico Tratante <span class="text-pc-red">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user-md absolute left-4 top-3.5 text-pc-blue/50 dark:text-slate-400 group-hover:text-pc-blue transition-colors"></i>
                            <input type="text" name="doctor_name" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="Nombre completo del médico..." required>
                        </div>
                    </div>

                    <div class="md:col-span-2 group">
                        <label class="label-pc mb-2">Institución Médica Emisora <span class="text-pc-red">*</span></label>
                        <div class="relative">
                            <i class="fas fa-hospital absolute left-4 top-3.5 text-pc-blue/50 dark:text-slate-400 group-hover:text-pc-blue transition-colors"></i>
                            <input type="text" name="issuing_institution" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="Ej. Hospital Uyapar, Clínica..." required>
                        </div>
                    </div>

                    <div class="md:col-span-2 group">
                        <label class="label-pc mb-2">Diagnóstico / Padecimiento <span class="text-gray-400 ml-1 font-bold">(Opcional)</span></label>
                        <div class="relative">
                            <i class="fas fa-notes-medical absolute left-4 top-3.5 text-pc-blue/50 dark:text-slate-400 group-hover:text-pc-blue transition-colors"></i>
                            <textarea name="medical_condition" rows="3" class="input-pc pl-12 py-3 dark:bg-slate-800 dark:border-slate-700 dark:text-white resize-none" placeholder="Detalles del diagnóstico, indicaciones adicionales..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-slate-800 mt-4 relative z-10">
                    <a href="{{ route('leaves.index') }}" class="btn-pc-secondary text-xs px-6 py-2.5">Cancelar</a>
                    <button type="submit" class="bg-gradient-to-r from-pc-red to-red-700 hover:from-red-600 hover:to-red-800 text-white font-black text-[10px] uppercase tracking-widest px-8 py-2.5 rounded-xl shadow-lg shadow-red-900/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-50 disabled:pointer-events-none" :disabled="!employee.id || isSubmitting">
                        <i class="fas fa-save mr-2" :class="{'fa-spin fa-spinner': isSubmitting}"></i> <span x-text="isSubmitting ? 'Guardando...' : 'Registrar Reposo Médico'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leaveForm', () => ({
            searchTerm: '',
            suggestions: [],
            showSuggestions: false,
            highlightIndex: -1,
            searchError: '',
            isSearching: false,
            isSubmitting: false,
            
            employee: { id: null, full_name: '', id_number: '', department: '' },
            form: { start_date: '', duration_value: 1, duration_unit: 'days', end_date: '' },
            notification: { show: false, type: 'error', message: '' },

            async fetchSuggestions() {
                if (this.searchTerm.length < 2) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                this.isSearching = true;
                try {
                    const response = await fetch(`{{ url('/api/employees/search') }}?q=${encodeURIComponent(this.searchTerm)}`);
                    if (response.ok) {
                        this.suggestions = await response.json();
                        this.showSuggestions = this.suggestions.length > 0;
                        this.highlightIndex = -1;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.isSearching = false;
                }
            },

            closeSuggestions() { this.showSuggestions = false; },
            highlightNext() {
                if (this.suggestions.length === 0) return;
                this.highlightIndex = (this.highlightIndex + 1) % this.suggestions.length;
                this.showSuggestions = true;
            },
            highlightPrev() {
                if (this.suggestions.length === 0) return;
                this.highlightIndex = (this.highlightIndex - 1 + this.suggestions.length) % this.suggestions.length;
                this.showSuggestions = true;
            },
            selectHighlighted() {
                if (this.highlightIndex >= 0 && this.highlightIndex < this.suggestions.length) {
                    this.selectEmployee(this.suggestions[this.highlightIndex]);
                }
            },
            async selectEmployee(emp) {
                const idNumber = emp.value || emp.id_number;
                this.searchTerm = emp.label || `${idNumber} - ${emp.full_name}`;
                this.suggestions = [];
                this.showSuggestions = false;
                this.searchError = '';
                this.isSearching = true;

                try {
                    const response = await fetch(`{{ url('/api/employees/by-id') }}/${idNumber}`);
                    if (response.ok) {
                        const data = await response.json();
                        this.employee = { id: data.id, full_name: data.full_name, id_number: data.id_number, department: data.department };
                    } else {
                        this.searchError = 'Error al cargar datos del empleado.';
                        this.employee.id = null;
                    }
                } catch (e) {
                    this.searchError = 'Error de conexión';
                    this.employee.id = null;
                } finally {
                    this.isSearching = false;
                }
            },
            clearSearch() {
                this.searchTerm = '';
                this.suggestions = [];
                this.showSuggestions = false;
                this.employee.id = null;
            },

            calculateEndDate() {
                let start = this.form.start_date;
                let value = parseInt(this.form.duration_value);
                let unit = this.form.duration_unit;

                if (!start || !value || isNaN(value)) {
                    this.form.end_date = '';
                    return;
                }

                let date = new Date(start);
                // Ajustar por zona horaria para evitar desfase
                date.setMinutes(date.getMinutes() + date.getTimezoneOffset());

                if (unit === 'days') date.setDate(date.getDate() + value);
                else if (unit === 'weeks') date.setDate(date.getDate() + value * 7);
                else if (unit === 'months') date.setMonth(date.getMonth() + value);
                
                this.form.end_date = date.toISOString().split('T')[0];
            },

            submitForm(e) {
                if (!this.employee.id) {
                    this.notification = { show: true, type: 'error', message: 'Debe seleccionar un empleado.' };
                    return;
                }
                this.isSubmitting = true;
                e.target.submit();
            }
        }));
    });
</script>
@endpush
@endsection