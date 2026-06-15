@extends('layouts.app')

@section('content')
<div class="py-4" x-data="vacationForm({{ $contingencyPlans->toJson() }})">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Encabezado Mejorado --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-pc-blue dark:text-white tracking-tight flex items-center gap-3">
                    <i class="fas fa-umbrella-beach text-pc-orange"></i> Solicitar Vacaciones
                </h1>
                <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-1">
                    Nueva solicitud de descanso y disfrute anual
                </p>
            </div>
            <a href="{{ route('vacations.index') }}" class="btn-pc-secondary text-xs px-4 py-2 flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al listado
            </a>
        </div>

        <div class="card-pc border-t-4 border-pc-orange overflow-hidden relative dark:bg-slate-900 dark:border-slate-800">
            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                <i class="fas fa-sun text-9xl text-pc-orange animate-spin-slow"></i>
            </div>

            {{-- Bloque de notificaciones dinámicas (controlado por Alpine) --}}
            <div x-show="notification.show" x-cloak class="m-5 mb-0">
                <div :class="{
                    'bg-red-50 dark:bg-red-950/20 border-l-4 border-red-500': notification.type === 'error',
                    'bg-green-50 dark:bg-green-950/20 border-l-4 border-green-500': notification.type === 'success',
                    'bg-yellow-50 dark:bg-yellow-950/20 border-l-4 border-yellow-500': notification.type === 'warning'
                }" class="p-4 rounded-r-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i :class="{
                                'fas fa-exclamation-circle text-red-500': notification.type === 'error',
                                'fas fa-check-circle text-green-500': notification.type === 'success',
                                'fas fa-exclamation-triangle text-yellow-500': notification.type === 'warning'
                            }"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold" :class="{
                                'text-red-700 dark:text-red-400': notification.type === 'error',
                                'text-green-700 dark:text-green-400': notification.type === 'success',
                                'text-yellow-700 dark:text-yellow-400': notification.type === 'warning'
                            }" x-text="notification.message"></p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" @click="notification.show = false" class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('vacations.store') }}" method="POST" id="vacation-form" class="p-5 relative z-10" @submit="submitForm">
                @csrf

                {{-- Búsqueda por cédula/nombre estilo Google --}}
                <div class="mb-6 relative">
                    <label for="employee_search" class="block text-[10px] font-black text-pc-blue dark:text-gray-300 mb-2 uppercase tracking-widest">
                        Buscar empleado por cédula o nombre <span class="text-pc-red">*</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="employee_search"
                               x-model="searchTerm"
                               @input.debounce.300ms="fetchSuggestions"
                               @keydown.escape="closeSuggestions"
                               @keydown.arrow-down.prevent="highlightNext"
                               @keydown.arrow-up.prevent="highlightPrev"
                               @keydown.enter.prevent="selectHighlighted"
                               @focus="fetchSuggestions"
                               placeholder="Escriba cédula o nombre (Ej: V-27297486 o Adrian)..."
                               aria-label="Buscar empleado por cédula o nombre"
                               class="w-full border-gray-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-inner focus:border-pc-orange focus:ring focus:ring-pc-orange/20 pl-11 py-3 text-sm transition-all input-pc">
                        <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <button type="button"
                                x-show="searchTerm"
                                @click="clearSearch"
                                aria-label="Limpiar búsqueda"
                                class="absolute z-10 inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    {{-- Indicador de carga durante búsqueda --}}
                    <div x-show="isSearching" class="absolute right-12 top-11">
                        <i class="fas fa-spinner fa-spin text-pc-orange"></i>
                    </div>
                    {{-- Lista de sugerencias --}}
                    <div x-show="suggestions.length > 0 && showSuggestions"
                         @click.away="closeSuggestions"
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl shadow-2xl max-h-60 overflow-auto divide-y divide-gray-50 dark:divide-slate-700/50">
                        <template x-for="(emp, index) in suggestions" :key="emp.id">
                            <div @click="selectEmployee(emp)"
                                 @mouseenter="highlightIndex = index"
                                 class="px-5 py-3 cursor-pointer hover:bg-pc-orange/5 dark:hover:bg-slate-700 flex justify-between items-center transition-colors"
                                 :class="{ 'bg-pc-orange/5 dark:bg-slate-700': highlightIndex === index }">
                                <div class="font-black text-gray-800 dark:text-gray-200 text-xs" x-text="emp.label || emp.full_name"></div>
                                <div class="text-[10px] font-bold text-pc-orange uppercase tracking-widest bg-orange-50 dark:bg-orange-500/10 px-2.5 py-0.5 rounded-full" x-text="emp.value || emp.id_number"></div>
                            </div>
                        </template>
                    </div>
                    <p x-show="searchError" class="mt-1 text-xs text-red-600 font-bold" x-text="searchError"></p>
                </div>

                {{-- Datos del empleado cargados y Saldos Premium --}}
                <div x-show="employee.id" x-transition.opacity.duration.400ms class="mb-8 p-6 bg-gradient-to-r from-orange-50/50 to-transparent dark:from-slate-800 dark:to-transparent rounded-2xl border border-orange-100 dark:border-slate-700 shadow-sm">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white dark:bg-slate-900 border-2 border-pc-orange text-pc-orange rounded-full flex items-center justify-center text-xl shadow-md">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-pc-orange uppercase tracking-widest mb-0.5">Colaborador Seleccionado</p>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight" x-text="employee.full_name"></h3>
                                <div class="flex items-center gap-3 mt-1 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    <span><i class="fas fa-briefcase text-gray-400 mr-1"></i> <span x-text="(employee.department || 'Sin depto.')"></span></span>
                                    <span>•</span>
                                    <span><span class="text-gray-400">Cargo:</span> <span x-text="(employee.position || 'Sin cargo')"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-left md:text-right bg-white dark:bg-slate-800 px-4 py-2 rounded-xl border border-gray-100 dark:border-slate-700 shadow-inner">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Antigüedad desde</p>
                            <p class="text-sm font-extrabold text-pc-blue dark:text-white" x-text="employee.hired_date ? formatDate(employee.hired_date) : 'S/I'"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-blue-100 dark:border-blue-900/30 flex flex-col items-center shadow-sm">
                            <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Saldo Regular</span>
                            <span class="text-3xl font-black text-pc-blue dark:text-blue-400" x-text="employee.regular_available"></span>
                            <span class="text-[9px] text-gray-400 font-bold uppercase mt-1">Días del año actual</span>
                        </div>
                        <div class="bg-red-50/50 dark:bg-red-950/20 p-4 rounded-xl border border-red-100 dark:border-red-900/30 flex flex-col items-center shadow-sm">
                            <span class="text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-wider mb-1">Días Vencidos</span>
                            <span class="text-3xl font-black text-red-600 dark:text-red-400" x-text="employee.accumulated_available"></span>
                            <span class="text-[9px] text-red-500 dark:text-red-400/70 font-black uppercase mt-1">De años anteriores</span>
                        </div>
                        <div class="bg-gradient-to-br from-pc-blue to-blue-700 dark:from-slate-800 dark:to-slate-950 p-4 rounded-xl text-white flex flex-col items-center shadow-md">
                            <span class="text-[10px] font-black opacity-80 uppercase tracking-wider mb-1">Total Disponible</span>
                            <span class="text-3xl font-black" x-text="employee.available_days"></span>
                            <span class="text-[9px] opacity-70 font-bold uppercase mt-1">Suma de ambos saldos</span>
                        </div>
                    </div>
                    <input type="hidden" name="employee_id" x-model="employee.id">
                </div>

                {{-- Fechas y Distribución de Días --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                    {{-- Bloque Fechas --}}
                    <div class="space-y-5 bg-gray-50/50 dark:bg-slate-800/20 p-5 rounded-2xl border border-gray-100 dark:border-slate-800">
                        <h4 class="text-xs font-black text-pc-blue dark:text-white uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-pc-orange"></i> Definición de Período
                        </h4>

                        <div class="group">
                            <label for="start_date" class="label-pc mb-2">Fecha de Inicio <span class="text-pc-red">*</span></label>
                            <div class="relative">
                                <i class="fas fa-calendar-plus absolute left-4 top-3.5 text-gray-400 group-hover:text-pc-orange transition-colors"></i>
                                <input type="date" name="start_date" id="start_date" x-model="form.start_date" @change="calculateEndDate" required min="{{ date('Y-m-d') }}"
                                       class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white @error('start_date') border-red-500 @enderror">
                            </div>
                            @error('start_date')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div class="group">
                            <label for="end_date" class="label-pc mb-2">Fecha de Culminación <span class="text-pc-red">*</span></label>
                            <div class="relative">
                                <i class="fas fa-calendar-check absolute left-4 top-3.5 text-gray-400"></i>
                                <input type="date" name="end_date" id="end_date" x-model="form.end_date" required min="{{ date('Y-m-d') }}"
                                       class="input-pc pl-12 bg-gray-100 dark:bg-slate-800/50 text-gray-500 dark:text-gray-400 font-bold border-dashed cursor-not-allowed" readonly>
                            </div>
                            <p class="text-[10px] text-pc-orange dark:text-orange-400 mt-2 font-black uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-info-circle text-pc-orange"></i> Se calcula omitiendo fines de semana y contingencias
                            </p>
                            @error('end_date')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Bloque Distribución Días --}}
                    <div class="bg-blue-50/40 dark:bg-slate-800/40 p-5 rounded-2xl border border-blue-100/50 dark:border-slate-800 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black text-pc-blue dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fas fa-chart-pie text-pc-blue dark:text-blue-400"></i> Distribución de Días
                            </h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label for="regular_days_to_take" class="label-pc mb-2">Días Regulares</label>
                                    <input type="number" name="regular_days_to_take" id="regular_days_to_take" 
                                           x-model="form.regular_days" @input="updateTotalDays" required min="0" :max="employee.regular_available"
                                           class="input-pc dark:bg-slate-800 dark:border-slate-700 dark:text-white text-center font-bold">
                                </div>
                                <div class="group">
                                    <label for="accumulated_days_to_take" class="label-pc mb-2 text-red-600 dark:text-red-400">Días Vencidos</label>
                                    <input type="number" name="accumulated_days_to_take" id="accumulated_days_to_take" 
                                           x-model="form.accumulated_days" @input="updateTotalDays" required min="0" :max="employee.accumulated_available"
                                           class="input-pc bg-red-50/50 text-red-900 border-red-200 dark:bg-slate-800 dark:border-slate-700 dark:text-red-400 text-center font-bold">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-blue-100 dark:border-slate-700 flex justify-between items-center">
                            <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Total a disfrutar:</span>
                            <div class="text-right">
                                <span class="text-3xl font-black text-pc-blue dark:text-blue-400"><span x-text="totalDays"></span></span>
                                <span class="text-xs font-black text-gray-400 uppercase tracking-wider ml-1">días</span>
                            </div>
                            <input type="hidden" name="days_taken" :value="totalDays">
                        </div>
                    </div>
                </div>

                <p x-show="daysError" x-cloak class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 rounded-xl flex items-center gap-2 text-red-600 dark:text-red-400 text-xs font-bold mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span x-text="daysError"></span>
                </p>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ route('vacations.index') }}" class="btn-pc-secondary text-xs px-5 py-2.5">Cancelar</a>
                    <button type="submit" class="bg-pc-orange hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-orange-100 dark:shadow-none transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2" :disabled="!employee.id || isSubmitting">
                        <i class="fas fa-save"></i>
                        <span x-text="isSubmitting ? 'Guardando...' : 'Guardar Solicitud'"></span>
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
        Alpine.data('vacationForm', (contingenciesData = []) => ({
            contingencies: contingenciesData,
            // Búsqueda
            searchTerm: '',
            suggestions: [],
            showSuggestions: false,
            highlightIndex: -1,
            searchError: '',
            isSearching: false,

            // Empleado seleccionado
            employee: {
                id: null,
                full_name: '',
                department: '',
                position: '',
                hired_date: null,
                available_days: 0,
                regular_available: 0,
                accumulated_available: 0
            },

            // Formulario
            form: {
                start_date: '',
                end_date: '',
                regular_days: 0,
                accumulated_days: 0
            },
            totalDays: 0,
            daysError: '',
            autoCalculate: true,
            isSubmitting: false,

            // Notificaciones
            notification: {
                show: false,
                type: 'error',
                message: ''
            },

            // Métodos de búsqueda
            async fetchSuggestions() {
                const term = this.searchTerm.trim();
                if (term.length < 2) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                this.isSearching = true;
                try {
                    const response = await fetch(`{{ url('/employees/autocomplete') }}?term=${encodeURIComponent(term)}`);
                    if (response.ok) {
                        const data = await response.json();
                        this.suggestions = data;
                        this.showSuggestions = data.length > 0;
                        this.highlightIndex = -1;
                    }
                } catch (e) {
                    console.error('Error fetching suggestions:', e);
                } finally {
                    this.isSearching = false;
                }
            },

            closeSuggestions() {
                this.showSuggestions = false;
                this.highlightIndex = -1;
            },

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
                    const response = await fetch(`{{ url('/api/employees/by-id') }}/${idNumber}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'include'
                    });
                    if (response.ok) {
                        const data = await response.json();
                        this.employee = {
                            id: data.id,
                            full_name: data.full_name,
                            department: data.department,
                            position: data.position,
                            hired_date: data.hired_date,
                            available_days: data.available_days,
                            regular_available: data.regular_available,
                            accumulated_available: data.accumulated_available
                        };
                        this.validateDays();
                    } else {
                        this.searchError = 'Error al cargar datos del empleado.';
                        this.resetEmployee();
                    }
                } catch (e) {
                    console.error(e);
                    this.searchError = 'Error de conexión';
                    this.resetEmployee();
                } finally {
                    this.isSearching = false;
                }
            },

            updateTotalDays() {
                this.totalDays = (parseInt(this.form.regular_days) || 0) + (parseInt(this.form.accumulated_days) || 0);
                this.calculateEndDate();
                this.validateDays();
            },

            clearSearch() {
                this.searchTerm = '';
                this.suggestions = [];
                this.showSuggestions = false;
                this.resetEmployee();
            },

            resetEmployee() {
                this.employee = {
                    id: null,
                    full_name: '',
                    department: '',
                    position: '',
                    hired_date: null,
                    available_days: 0,
                    regular_available: 0,
                    accumulated_available: 0
                };
                this.totalDays = 0;
                this.form.regular_days = 0;
                this.form.accumulated_days = 0;
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            },

            calculateEndDate() {
                if (!this.form.start_date || this.totalDays <= 0) {
                    this.form.end_date = '';
                    return;
                }

                let remainingDays = this.totalDays;
                let curDate = new Date(this.form.start_date + 'T00:00:00');
                
                // Mientras queden días por asignar
                while (remainingDays > 0) {
                    const dayOfWeek = curDate.getDay();
                    const dateString = curDate.toISOString().split('T')[0];
                    
                    // Comprobar si es fin de semana
                    const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                    
                    // Comprobar si está en contingencia
                    let isContingency = false;
                    for (let plan of this.contingencies) {
                        if (dateString >= plan.start_date && dateString <= plan.end_date) {
                            isContingency = true;
                            break;
                        }
                    }

                    // Si no es fin de semana ni contingencia, consumimos 1 día solicitado
                    if (!isWeekend && !isContingency) {
                        remainingDays--;
                    }

                    // Avanzar al siguiente día, solo si quedan días (para no avanzar de más al final)
                    if (remainingDays > 0) {
                        curDate.setDate(curDate.getDate() + 1);
                    }
                }

                this.form.end_date = curDate.toISOString().split('T')[0];
                this.validateDays();
            },

            updateTotalDays() {
                const reg = parseInt(this.form.regular_days) || 0;
                const acc = parseInt(this.form.accumulated_days) || 0;
                this.totalDays = reg + acc;
                this.calculateEndDate();
            },

            validateDays() {
                const reg = parseInt(this.form.regular_days) || 0;
                const acc = parseInt(this.form.accumulated_days) || 0;

                if (reg > this.employee.regular_available) {
                    this.daysError = `No puede solicitar más de ${this.employee.regular_available} días regulares`;
                    return false;
                }
                if (acc > this.employee.accumulated_available) {
                    this.daysError = `No puede solicitar más de ${this.employee.accumulated_available} días acumulados`;
                    return false;
                }
                if (reg + acc <= 0 && this.employee.id) {
                    this.daysError = 'Debe solicitar al menos 1 día total';
                    return false;
                }
                this.daysError = '';
                return true;
            },

            showNotification(message, type = 'error') {
                this.notification = {
                    show: true,
                    type: type,
                    message: message
                };
                // Ocultar automáticamente después de 5 segundos
                setTimeout(() => {
                    this.notification.show = false;
                }, 5000);
            },

            // ========== ENVÍO CORREGIDO ==========
            submitForm(e) {
                e.preventDefault();

                if (!this.employee.id) {
                    this.showNotification('Debe seleccionar un empleado válido', 'error');
                    return false;
                }
                if (!this.validateDays()) {
                    this.showNotification(this.daysError, 'error');
                    return false;
                }

                this.isSubmitting = true;

                const form = document.getElementById('vacation-form');
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (response.ok) {
                        // Éxito: redirigir
                        window.location.href = data.redirect || '{{ route("vacations.index") }}';
                    } else {
                        // Error controlado
                        const errorMessage = data.error || 'Error al guardar la solicitud.';
                        this.showNotification(errorMessage, 'error');
                        this.isSubmitting = false;
                    }
                })
                .catch(error => {
                    console.error('Error de red:', error);
                    this.showNotification('Error de conexión. Intente nuevamente.', 'error');
                    this.isSubmitting = false;
                });

                return false;
            }
        }));
    });
</script>
@endpush