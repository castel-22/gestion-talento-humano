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
                <span class="text-sm text-pc-orange font-medium">Planes de Contingencia</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto" x-data="contingenciesManager()">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-shield-alt text-pc-red"></i> Planes de Contingencia
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Gestión de períodos con restricciones de salida vacacional</p>
        </div>
        <button @click="openModal()" class="bg-pc-red hover:bg-red-700 text-white font-black text-[10px] uppercase px-8 py-4 rounded-xl shadow-lg shadow-red-100 transition-all flex items-center gap-2">
            <i class="fas fa-calendar-plus"></i> Registrar Bloqueo
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Listado de Planes --}}
        <div class="lg:col-span-2 space-y-6">
            <template x-for="plan in plans" :key="plan.id">
                <div class="card-pc p-6 group hover:border-pc-red transition-all dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex justify-between items-start">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-pc-red/10 text-pc-red">
                                <i class="fas fa-lock text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-black text-pc-blue dark:text-gray-100 uppercase" x-text="plan.name"></h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase" x-text="formatDate(plan.start_date)"></span>
                                    <i class="fas fa-arrow-right text-[8px] text-pc-red"></i>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase" x-text="formatDate(plan.end_date)"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                            <button @click="openModal(plan)" class="w-8 h-8 flex items-center justify-center bg-gray-50 dark:bg-slate-800 rounded-lg text-pc-blue hover:bg-pc-blue hover:text-white transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <button @click="deletePlan(plan.id)" class="w-8 h-8 flex items-center justify-center bg-gray-50 dark:bg-slate-800 rounded-lg text-pc-red hover:bg-pc-red hover:text-white transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium italic" x-text="plan.description || 'Sin observaciones registradas'"></p>
                    </div>
                </div>
            </template>

            <div x-show="plans.length === 0" class="text-center py-20 bg-gray-50/50 dark:bg-slate-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-slate-800">
                <i class="fas fa-shield-virus text-gray-200 dark:text-slate-800 text-5xl mb-4"></i>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No hay planes de contingencia activos</p>
            </div>
        </div>

        {{-- Calendarios de Referencia --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="card-pc p-6 dark:bg-slate-900 dark:border-slate-800">
                <h3 class="text-sm font-black text-pc-blue dark:text-white uppercase mb-6 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-pc-orange"></i> Vista Operativa
                </h3>
                <div class="space-y-8">
                    <template x-for="month in calendars" :key="month.key">
                        <div>
                            <p class="text-[10px] font-black text-pc-blue dark:text-pc-orange uppercase text-center mb-4 tracking-widest" x-text="month.name"></p>
                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="d in ['L','M','M','J','V','S','D']">
                                    <div class="text-[8px] font-black text-gray-300 text-center py-1" x-text="d"></div>
                                </template>
                                <template x-for="week in month.weeks">
                                    <template x-for="day in week">
                                        <div class="aspect-square flex items-center justify-center rounded-lg text-[9px] transition-all"
                                             :class="day.blocked ? 'bg-pc-red text-white font-black shadow-lg shadow-red-100' : (day.currentMonth ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' : 'opacity-10')">
                                            <span x-text="day.day"></span>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Registro --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 bg-pc-blue/40 backdrop-blur-md flex items-center justify-center z-50 p-4" x-transition.opacity>
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-md border border-gray-100 dark:border-slate-800" @click.away="modalOpen = false">
            <h3 class="text-lg font-black text-pc-blue dark:text-white uppercase mb-6 flex items-center gap-3">
                <i class="fas fa-calendar-check text-pc-orange"></i> <span x-text="editing ? 'Actualizar Plan' : 'Nuevo Bloqueo'"></span>
            </h3>
            <form @submit.prevent="savePlan" class="space-y-6">
                <div>
                    <label class="label-pc">Nombre del Evento / Operativo</label>
                    <input type="text" x-model="form.name" required class="input-pc" placeholder="Ej: Operativo Carnaval 2026">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="label-pc">Fecha Inicio</label><input type="date" x-model="form.start_date" required class="input-pc"></div>
                    <div><label class="label-pc">Fecha Fin</label><input type="date" x-model="form.end_date" required class="input-pc"></div>
                </div>
                <div>
                    <label class="label-pc">Motivo del Bloqueo</label>
                    <textarea x-model="form.description" rows="3" class="input-pc" placeholder="Justificación técnica de la restricción..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="modalOpen = false" class="text-[10px] font-black text-gray-400 uppercase px-6">Cancelar</button>
                    <button type="submit" class="bg-pc-blue text-white font-black text-[10px] uppercase px-8 py-3 rounded-xl shadow-lg shadow-blue-100 hover:bg-blue-800 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function contingenciesManager() {
        return {
            plans: @json($plans),
            calendars: [],
            modalOpen: false,
            editing: false,
            form: { id: null, name: '', start_date: '', end_date: '', description: '' },

            init() {
                this.generateCalendars();
                this.$watch('plans', () => this.generateCalendars());
            },

            generateCalendars() {
                const months = [];
                const now = new Date();
                for (let i = 0; i < 3; i++) {
                    const date = new Date(now.getFullYear(), now.getMonth() + i, 1);
                    months.push({
                        key: i,
                        name: date.toLocaleString('es-ES', { month: 'long', year: 'numeric' }),
                        weeks: this.getWeeks(date.getFullYear(), date.getMonth())
                    });
                }
                this.calendars = months;
            },

            getWeeks(year, month) {
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                let startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
                
                const weeks = [];
                let week = Array(7).fill(null);
                
                for (let i = 0; i < startDay; i++) {
                    const prev = new Date(year, month, -startDay + i + 1);
                    week[i] = { day: prev.getDate(), currentMonth: false, blocked: false };
                }

                for (let day = 1; day <= lastDay.getDate(); day++) {
                    const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const blocked = this.plans.some(p => fullDate >= p.start_date && fullDate <= p.end_date);
                    
                    week[startDay] = { day, currentMonth: true, blocked };
                    startDay++;
                    if (startDay === 7) {
                        weeks.push(week);
                        week = Array(7).fill(null);
                        startDay = 0;
                    }
                }

                if (startDay !== 0) {
                    for (let i = startDay; i < 7; i++) {
                        week[i] = { day: i - startDay + 1, currentMonth: false, blocked: false };
                    }
                    weeks.push(week);
                }
                return weeks;
            },

            formatDate(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
            },

            openModal(plan = null) {
                if (plan) {
                    this.editing = true;
                    this.form = { ...plan };
                } else {
                    this.editing = false;
                    this.form = { id: null, name: '', start_date: '', end_date: '', description: '' };
                }
                this.modalOpen = true;
            },

            async savePlan() {
                const method = this.editing ? 'PUT' : 'POST';
                const url = this.editing ? `{{ url('/vacations/contingencies') }}/${this.form.id}` : '{{ url('/vacations/contingencies') }}';
                
                try {
                    const res = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    if (res.ok) window.location.reload();
                } catch (e) { console.error(e); }
            },

            async deletePlan(id) {
                if (!confirm('¿Eliminar este plan de contingencia?')) return;
                try {
                    const res = await fetch(`{{ url('/vacations/contingencies') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    if (res.ok) window.location.reload();
                } catch (e) { console.error(e); }
            }
        };
    }
</script>
@endpush
