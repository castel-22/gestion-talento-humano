@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Control de Vacaciones</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div id="vacations-ongoing-data" data-ongoing='@json($ongoingVacations ?? [])' style="display: none;"></div>

<div class="max-w-7xl mx-auto" x-data="vacationsManager()">
    
    {{-- Cabecera con estadísticas --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h2 class="text-2xl font-black text-pc-blue uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-umbrella-beach text-pc-orange"></i> Gestión de Vacaciones
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Administración de períodos de descanso institucional</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button @click="toggleContingencyPanel" class="bg-gray-800 hover:bg-black text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-shield-alt"></i> <span x-text="showContingencyPanel ? 'Ocultar Planes' : 'Planes de Contingencia'"></span>
            </button>
            <a href="{{ route('vacations.create') }}" class="bg-pc-orange hover:bg-orange-600 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-orange-100 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Nueva Solicitud
            </a>
        </div>
    </div>

    {{-- Panel de Contingencia Modernizado --}}
    <div x-show="showContingencyPanel" x-cloak x-collapse class="mb-8 overflow-hidden rounded-3xl border border-pc-red/20 bg-pc-red/[0.02] p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="text-lg font-black text-pc-red uppercase flex items-center gap-3">
                    <i class="fas fa-lock"></i> Zonas de Bloqueo Operativo
                </h3>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">Períodos restringidos para solicitudes de descanso</p>
            </div>
            <button @click="openContingencyModal(null)" class="bg-white border border-pc-red/20 text-pc-red font-black text-[9px] uppercase px-4 py-2 rounded-lg hover:bg-pc-red hover:text-white transition-all">
                Crear Nuevo Plan
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <template x-for="plan in contingencies" :key="plan.id">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full animate-pulse" :class="isPlanActive(plan) ? 'bg-pc-red' : 'bg-gray-300'"></div>
                            <span class="text-[10px] font-black text-pc-blue uppercase truncate max-w-[150px]" x-text="plan.name"></span>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openContingencyModal(plan)" class="text-pc-blue p-1.5 hover:bg-gray-50 rounded-lg"><i class="fas fa-edit text-[10px]"></i></button>
                            <button @click="deleteContingency(plan.id)" class="text-pc-red p-1.5 hover:bg-gray-50 rounded-lg"><i class="fas fa-trash text-[10px]"></i></button>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">
                        <span x-text="formatDate(plan.start_date)"></span>
                        <i class="fas fa-long-arrow-alt-right text-pc-red"></i>
                        <span x-text="formatDate(plan.end_date)"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Calendarios de Bloqueo Compactos --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 bg-white/50 p-6 rounded-2xl border border-gray-100">
            <template x-for="month in nextThreeMonths" :key="month.key">
                <div class="flex flex-col">
                    <h5 class="text-[10px] font-black text-pc-blue uppercase text-center mb-4 tracking-widest border-b border-gray-100 pb-2" x-text="month.name"></h5>
                    <div class="grid grid-cols-7 gap-1">
                        <template x-for="dayName in ['L','M','M','J','V','S','D']">
                            <div class="text-[8px] font-black text-gray-300 text-center py-1 uppercase" x-text="dayName"></div>
                        </template>
                        <template x-for="(week, wIndex) in month.weeks" :key="wIndex">
                            <template x-for="day in week" :key="day.date">
                                <div class="aspect-square flex items-center justify-center rounded-lg transition-all"
                                     :class="day.blocked ? 'bg-pc-red text-white shadow-sm font-black' : (day.currentMonth ? 'text-gray-600 hover:bg-white' : 'opacity-20')">
                                    <span class="text-[9px]" x-text="day.day"></span>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Filtros y Buscador Compacto --}}
    <div class="card-pc p-4 mb-8">
        <form method="GET" action="{{ route('vacations.index') }}" class="flex flex-wrap md:flex-nowrap gap-4 items-center">
            @php
                $selectedEmp = request('employee_id') ? $employees->firstWhere('id', request('employee_id')) : null;
            @endphp
            <div class="flex-1 min-w-[200px] relative" x-data="{
                open: false,
                search: '{{ $selectedEmp ? $selectedEmp->full_name : '' }}',
                selectedId: '{{ request('employee_id', '') }}',
                results: [],
                loading: false,
                fetchResults() {
                    if (this.search.length < 2) {
                        this.results = [];
                        this.open = false;
                        return;
                    }
                    this.loading = true;
                    fetch('{{ route('api.employees.autocomplete') }}?term=' + encodeURIComponent(this.search))
                        .then(res => res.json())
                        .then(data => {
                            this.results = data;
                            this.loading = false;
                            this.open = true;
                        })
                        .catch(() => { this.loading = false; });
                },
                selectItem(item) {
                    this.selectedId = item.id;
                    this.search = item.label;
                    this.open = false;
                },
                clear() {
                    this.selectedId = '';
                    this.search = '';
                    this.results = [];
                    this.open = false;
                }
            }">
                <i class="fas fa-user-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                
                <div class="relative flex items-center w-full">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="fetchResults()"
                           @focus="open = true"
                           @click.away="setTimeout(() => { open = false; }, 200)"
                           placeholder="FILTRAR POR INTEGRANTE..."
                           class="input-pc pl-12 py-3 text-[10px] font-black uppercase tracking-widest border-none bg-gray-50/50 w-full pr-8">
                    
                    <div class="absolute right-4 flex items-center space-x-1">
                        <template x-if="loading">
                            <i class="fas fa-spinner animate-spin text-gray-400 text-[10px]"></i>
                        </template>
                        <template x-if="!loading && search">
                            <button type="button" @click="clear()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </template>
                    </div>
                </div>
                
                <input type="hidden" name="employee_id" :value="selectedId">

                <div x-show="open && results.length > 0"
                     x-cloak
                     class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto">
                    <ul class="py-1 text-[11px] text-gray-700 font-medium">
                        <template x-for="item in results" :key="item.id">
                            <li>
                                <button type="button"
                                        @click="selectItem(item)"
                                        class="w-full text-left px-3 py-2 hover:bg-pc-orange hover:text-white transition-colors">
                                    <span x-text="item.label"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
            <div class="w-32">
                <select name="year" class="input-pc py-3 text-[10px] font-black uppercase tracking-widest border-none bg-gray-50/50 text-center">
                    <option value="">Año</option>
                    @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-pc-blue text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase shadow-md transition-all hover:bg-blue-800">Filtrar</button>
            <a href="{{ route('vacations.index') }}" class="text-[10px] font-black text-gray-400 uppercase px-4">Limpiar</a>
        </form>
    </div>

    {{-- Tabs de Estado --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-gray-100 dark:shadow-none overflow-hidden border border-gray-100 dark:border-slate-800 transition-colors">
        <div class="flex border-b border-gray-50 dark:border-slate-800/50 p-1.5 bg-gray-50/50 dark:bg-slate-800/30 transition-colors">
            <button @click="activeTab = 'approved'" :class="activeTab === 'approved' ? 'bg-white dark:bg-slate-800 text-pc-blue dark:text-white shadow-sm' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'" class="flex-1 py-3 lg:py-4 px-2 lg:px-6 rounded-xl lg:rounded-2xl text-[9px] lg:text-[10px] font-black uppercase tracking-wider lg:tracking-widest transition-all flex flex-col sm:flex-row items-center justify-center gap-1.5 lg:gap-3">
                <i class="fas fa-calendar-check text-xs lg:text-sm"></i>
                <span class="text-center">Aprobadas</span>
                <span class="bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 px-2 py-0.5 rounded-lg text-[8px] lg:text-[9px] font-black">{{ $approvedVacations->total() }}</span>
            </button>
            <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'bg-white dark:bg-slate-800 text-pc-blue dark:text-white shadow-sm' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'" class="flex-1 py-3 lg:py-4 px-2 lg:px-6 rounded-xl lg:rounded-2xl text-[9px] lg:text-[10px] font-black uppercase tracking-wider lg:tracking-widest transition-all flex flex-col sm:flex-row items-center justify-center gap-1.5 lg:gap-3">
                <i class="fas fa-clock text-xs lg:text-sm"></i>
                <span class="text-center">Pendientes</span>
                <span class="bg-yellow-100 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 px-2 py-0.5 rounded-lg text-[8px] lg:text-[9px] font-black">{{ $pendingVacations->total() }}</span>
            </button>
            <button @click="activeTab = 'paused'" :class="activeTab === 'paused' ? 'bg-white dark:bg-slate-800 text-pc-blue dark:text-white shadow-sm' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'" class="flex-1 py-3 lg:py-4 px-2 lg:px-6 rounded-xl lg:rounded-2xl text-[9px] lg:text-[10px] font-black uppercase tracking-wider lg:tracking-widest transition-all flex flex-col sm:flex-row items-center justify-center gap-1.5 lg:gap-3">
                <i class="fas fa-pause-circle text-xs lg:text-sm"></i>
                <span class="text-center">Pausadas</span>
                <span class="bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 px-2 py-0.5 rounded-lg text-[8px] lg:text-[9px] font-black">{{ $pausedVacations->total() }}</span>
            </button>
        </div>

        <div class="p-0">
            {{-- Aprobadas --}}
            <div x-show="activeTab === 'approved'" x-transition:enter="transition ease-out duration-200" class="divide-y divide-gray-50 dark:divide-slate-800/50">
                <div class="p-4 bg-white/80 dark:bg-slate-900/80 border-b border-gray-50 dark:border-slate-800/50 flex gap-2 transition-colors">
                    <button type="button" @click="openInterruptModal(null)" class="bg-pc-orange/10 text-pc-orange hover:bg-pc-orange hover:text-white px-4 py-2 rounded-lg text-[9px] font-black uppercase transition-all flex items-center gap-2">
                        <i class="fas fa-pause"></i> Interrumpir Seleccionados
                    </button>
                </div>
                @include('partials.vacation-table', ['vacations' => $approvedVacations, 'context' => 'approved'])
                <div class="p-6 bg-gray-50/30 dark:bg-slate-800/20 transition-colors">{{ $approvedVacations->appends(request()->except('approved_page'))->links() }}</div>
            </div>

            {{-- Pendientes --}}
            <div x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-200" class="divide-y divide-gray-50 dark:divide-slate-800/50">
                <div class="p-4 bg-white/80 dark:bg-slate-900/80 border-b border-gray-50 dark:border-slate-800/50 flex gap-2 transition-colors">
                    <button type="button" data-mass-approve="{{ route('vacations.mass.approve') }}" class="bg-green-50 text-green-600 hover:bg-green-600 hover:text-white px-4 py-2 rounded-lg text-[9px] font-black uppercase transition-all flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Aprobar Lote
                    </button>
                    <button type="button" data-mass-reject="{{ route('vacations.mass.reject') }}" class="bg-pc-red/5 text-pc-red hover:bg-pc-red hover:text-white px-4 py-2 rounded-lg text-[9px] font-black uppercase transition-all flex items-center gap-2">
                        <i class="fas fa-times-circle"></i> Rechazar Lote
                    </button>
                </div>
                @include('partials.vacation-table', ['vacations' => $pendingVacations, 'context' => 'pending'])
                <div class="p-6 bg-gray-50/30 dark:bg-slate-800/20 transition-colors">{{ $pendingVacations->appends(request()->except('pending_page'))->links() }}</div>
            </div>

            {{-- Pausadas --}}
            <div x-show="activeTab === 'paused'" x-transition:enter="transition ease-out duration-200">
                @include('partials.vacation-table', ['vacations' => $pausedVacations, 'context' => 'paused'])
                <div class="p-6 bg-gray-50/30 dark:bg-slate-800/20 transition-colors">{{ $pausedVacations->appends(request()->except('paused_page'))->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Modal de Contingencia --}}
    <div x-show="contingencyModalOpen" x-cloak class="fixed inset-0 bg-pc-blue/40 backdrop-blur-md flex items-center justify-center z-50 p-4" x-transition.opacity>
        <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md border border-gray-100" @click.away="contingencyModalOpen = false">
            <h3 class="text-lg font-black text-pc-blue uppercase mb-6 flex items-center gap-3">
                <i class="fas fa-calendar-plus text-pc-orange"></i> <span x-text="editingContingency ? 'Ajustar Plan' : 'Nuevo Período de Bloqueo'"></span>
            </h3>
            <form @submit.prevent="saveContingency" class="space-y-6">
                <div>
                    <label class="label-pc">Nombre del Operativo / Contingencia</label>
                    <input type="text" x-model="contingencyForm.name" required class="input-pc" placeholder="Ej: Plan República 2026">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="label-pc">Inicio</label><input type="date" x-model="contingencyForm.start_date" required class="input-pc"></div>
                    <div><label class="label-pc">Cierre</label><input type="date" x-model="contingencyForm.end_date" required class="input-pc"></div>
                </div>
                <div><label class="label-pc">Observaciones Institucionales</label><textarea x-model="contingencyForm.description" rows="3" class="input-pc" placeholder="Justificación técnica del bloqueo..."></textarea></div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="contingencyModalOpen = false" class="text-[10px] font-black text-gray-400 uppercase px-6">Cancelar</button>
                    <button type="submit" class="bg-pc-blue text-white font-black text-[10px] uppercase px-8 py-3 rounded-xl shadow-lg shadow-blue-100 hover:bg-blue-800 transition-all">Guardar Plan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de Interrupción --}}
    <div x-show="interruptModalOpen" x-cloak class="fixed inset-0 bg-pc-blue/40 backdrop-blur-md flex items-center justify-center z-50 p-4" x-transition.opacity>
        <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md border border-gray-100" @click.away="interruptModalOpen = false">
            <h3 class="text-lg font-black text-orange-600 uppercase mb-6 flex items-center gap-3">
                <i class="fas fa-pause-circle text-pc-orange"></i> Interrumpir Vacaciones
            </h3>
            <form @submit.prevent="submitInterrupt" class="space-y-6">
                <div>
                    <label class="label-pc">Motivo de la Interrupción <span class="text-red-500">*</span></label>
                    <textarea x-model="interruptForm.reason" rows="3" required class="input-pc" placeholder="Describa el motivo institucional de la interrupción..."></textarea>
                </div>
                <p class="text-[10px] text-gray-400 font-bold" x-show="interruptForm.vacationId">
                    <i class="fas fa-info-circle mr-1"></i> Se interrumpirá la solicitud seleccionada.
                </p>
                <p class="text-[10px] text-gray-400 font-bold" x-show="!interruptForm.vacationId">
                    <i class="fas fa-info-circle mr-1"></i> Se interrumpirán todas las solicitudes marcadas con checkbox.
                </p>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="interruptModalOpen = false" class="text-[10px] font-black text-gray-400 uppercase px-6">Cancelar</button>
                    <button type="submit" class="bg-pc-orange text-white font-black text-[10px] uppercase px-8 py-3 rounded-xl shadow-lg shadow-orange-100 hover:bg-orange-600 transition-all">
                        <i class="fas fa-pause mr-1"></i> Confirmar Interrupción
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Formularios técnicos ocultos --}}
<form id="mass-actions-form" method="POST" style="display: none;">@csrf</form>
<form id="single-interrupt-form" method="POST" style="display: none;">@csrf <input type="hidden" name="interruption_reason" id="single-interrupt-reason"></form>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vacationsManager', () => ({
            activeTab: 'approved',
            showContingencyPanel: false,
            contingencyModalOpen: false,
            editingContingency: false,
            contingencies: @json($contingencyPlans ?? []),
            nextThreeMonths: [],
            contingencyForm: {
                id: null,
                name: '',
                start_date: '',
                end_date: '',
                description: ''
            },


            toggleContingencyPanel() {
                this.showContingencyPanel = !this.showContingencyPanel;
            },

            generateCalendars() {
                const months = [];
                const now = new Date();
                
                for (let i = 0; i < 3; i++) {
                    const date = new Date(now.getFullYear(), now.getMonth() + i, 1);
                    const monthName = date.toLocaleString('es-ES', { month: 'long', year: 'numeric' });
                    const weeks = this.getWeeksForMonth(date.getFullYear(), date.getMonth());
                    
                    months.push({
                        key: `${date.getFullYear()}-${date.getMonth()}`,
                        name: monthName,
                        weeks: weeks
                    });
                }
                this.nextThreeMonths = months;
            },

            getWeeksForMonth(year, month) {
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                
                // Ajustar al lunes (0=lunes en nuestro diseño de 7 días L-D)
                let startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
                
                const weeks = [];
                let currentWeek = Array(7).fill(null);
                
                // Días del mes anterior (opcional, para llenar huecos)
                const prevMonthLastDay = new Date(year, month, 0).getDate();
                for (let i = 0; i < startDay; i++) {
                    currentWeek[i] = { day: prevMonthLastDay - startDay + i + 1, currentMonth: false, date: null };
                }

                for (let day = 1; day <= lastDay.getDate(); day++) {
                    const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const isBlocked = this.contingencies.some(plan => {
                        return fullDate >= plan.start_date && fullDate <= plan.end_date;
                    });

                    currentWeek[startDay] = {
                        day: day,
                        currentMonth: true,
                        date: fullDate,
                        blocked: isBlocked
                    };

                    startDay++;
                    if (startDay === 7) {
                        weeks.push(currentWeek);
                        currentWeek = Array(7).fill(null);
                        startDay = 0;
                    }
                }

                if (startDay !== 0) {
                    // Rellenar días del mes siguiente
                    let nextDay = 1;
                    for (let i = startDay; i < 7; i++) {
                        currentWeek[i] = { day: nextDay++, currentMonth: false, date: null };
                    }
                    weeks.push(currentWeek);
                }

                return weeks;
            },

            isPlanActive(plan) {
                const now = new Date().toISOString().split('T')[0];
                return now >= plan.start_date && now <= plan.end_date;
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
            },

            openContingencyModal(plan = null) {
                if (plan) {
                    this.editingContingency = true;
                    this.contingencyForm = { ...plan };
                } else {
                    this.editingContingency = false;
                    this.contingencyForm = { id: null, name: '', start_date: '', end_date: '', description: '' };
                }
                this.contingencyModalOpen = true;
            },

            async saveContingency() {
                const url = this.editingContingency 
                    ? `/vacations/contingencies/${this.contingencyForm.id}`
                    : '/vacations/contingencies';
                
                const method = this.editingContingency ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.contingencyForm)
                    });

                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Error al guardar el plan de contingencia');
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            },

            async deleteContingency(id) {
                if (!confirm('¿Seguro que desea eliminar este plan de contingencia?')) return;

                try {
                    const response = await fetch(`{{ url('/vacations/contingencies') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            },

            // ============ INTERRUPTION MODAL ============
            interruptModalOpen: false,
            interruptForm: {
                vacationId: null,
                reason: ''
            },

            openInterruptModal(vacationId = null) {
                this.interruptForm.vacationId = vacationId;
                this.interruptForm.reason = '';
                this.interruptModalOpen = true;
            },

            async submitInterrupt() {
                if (!this.interruptForm.reason.trim()) return;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                if (this.interruptForm.vacationId) {
                    // Interrupción individual
                    const form = document.getElementById('single-interrupt-form');
                    form.action = `/vacations/${this.interruptForm.vacationId}/interrupt`;
                    document.getElementById('single-interrupt-reason').value = this.interruptForm.reason;
                    form.submit();
                } else {
                    // Interrupción masiva
                    const selected = this.getSelectedIds();
                    if (selected.length === 0) {
                        alert('Seleccione al menos una solicitud.');
                        return;
                    }

                    const form = document.getElementById('mass-actions-form');
                    form.action = '{{ route("vacations.mass.interrupt") }}';

                    // Limpiar inputs previos
                    form.querySelectorAll('input[name="ids[]"], input[name="interruption_reason"]').forEach(el => el.remove());

                    selected.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'interruption_reason';
                    reasonInput.value = this.interruptForm.reason;
                    form.appendChild(reasonInput);

                    form.submit();
                }

                this.interruptModalOpen = false;
            },

            // ============ SELECT ALL & MASS ACTIONS ============
            getSelectedIds() {
                return Array.from(document.querySelectorAll('.vacation-checkbox:checked')).map(cb => cb.value);
            },

            init() {
                this.generateCalendars();
                this.$watch('contingencies', () => this.generateCalendars());

                // Select-all checkboxes
                this.$nextTick(() => {
                    document.querySelectorAll('[id^="select-all-"]').forEach(selectAll => {
                        selectAll.addEventListener('change', function() {
                            const table = this.closest('table');
                            if (table) {
                                table.querySelectorAll('.vacation-checkbox').forEach(cb => {
                                    cb.checked = selectAll.checked;
                                });
                            }
                        });
                    });

                    // Mass approve button
                    const massApproveBtn = document.querySelector('[data-mass-approve]');
                    if (massApproveBtn) {
                        massApproveBtn.addEventListener('click', () => {
                            const ids = this.getSelectedIds();
                            if (ids.length === 0) { alert('Seleccione al menos una solicitud.'); return; }
                            if (!confirm(`¿Aprobar ${ids.length} solicitud(es)?`)) return;

                            const form = document.getElementById('mass-actions-form');
                            form.action = massApproveBtn.dataset.massApprove;
                            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                            ids.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
                                form.appendChild(input);
                            });
                            form.submit();
                        });
                    }

                    // Mass reject button
                    const massRejectBtn = document.querySelector('[data-mass-reject]');
                    if (massRejectBtn) {
                        massRejectBtn.addEventListener('click', () => {
                            const ids = this.getSelectedIds();
                            if (ids.length === 0) { alert('Seleccione al menos una solicitud.'); return; }
                            if (!confirm(`¿Rechazar ${ids.length} solicitud(es)?`)) return;

                            const form = document.getElementById('mass-actions-form');
                            form.action = massRejectBtn.dataset.massReject;
                            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                            ids.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
                                form.appendChild(input);
                            });
                            form.submit();
                        });
                    }
                });
            },
        }));
    });
</script>
@endpush