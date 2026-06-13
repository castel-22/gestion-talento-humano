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
                <a href="{{ route('guard-rotations.index') }}" class="text-sm text-gray-700 hover:text-pc-orange">Rotaciones</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Planificación de Roles</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto"
     x-data="guardCalendar({{ $guardRotation->id }}, {{ $date->year }}, {{ $date->month }})"
     data-can-update="{{ auth()->user()->can('update', $guardRotation) ? '1' : '0' }}">
    
    <div class="card-pc p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="bg-pc-blue p-3 rounded-2xl shadow-lg shadow-blue-100">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-pc-blue tracking-tight uppercase">{{ $guardRotation->name }}</h2>
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">{{ $guardRotation->description ?: 'Rotación de guardia activa' }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                @can('update', $guardRotation)
                <form method="POST" action="{{ route('guard-rotations.generate', $guardRotation) }}" class="inline">
                    @csrf
                    <input type="hidden" name="month" :value="month">
                    <input type="hidden" name="year" :value="year">
                    <button type="submit" class="bg-pc-orange hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-xs font-black shadow-lg shadow-orange-100 transition-all flex items-center gap-2">
                        <i class="fas fa-magic"></i> GENERAR SECUENCIA
                    </button>
                </form>
                @endcan
                <a href="{{ route('guard-rotations.pdf', ['guard_rotation' => $guardRotation, 'month' => $date->month, 'year' => $date->year]) }}"
                   class="bg-pc-red hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-xs font-black shadow-lg shadow-red-100 transition-all flex items-center gap-2" target="_blank">
                    <i class="fas fa-file-pdf"></i> EXPORTAR ROL
                </a>
                <a href="{{ route('guard-rotations.index') }}" class="bg-white border-2 border-gray-100 hover:border-pc-blue text-pc-blue px-5 py-2.5 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> VOLVER
                </a>
            </div>
        </div>

        {{-- Navegación de Mes --}}
        <div class="flex items-center justify-between mt-10 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
            <button @click="changeMonth(-1)" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl hover:bg-pc-blue hover:text-white transition-all shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="text-center">
                <h3 class="text-xl font-black text-pc-blue uppercase tracking-tighter" x-text="currentMonthName"></h3>
                <p class="text-[10px] font-black text-pc-orange tracking-[0.2em]" x-text="year"></p>
            </div>
            <button @click="changeMonth(1)" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl hover:bg-pc-blue hover:text-white transition-all shadow-sm">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    {{-- Calendario Físico / Planilla --}}
    <div class="bg-white p-6 md:p-12 shadow-2xl mx-auto border border-gray-200 mt-6 print:shadow-none print:border-none print:p-0">
        {{-- Cabecera Institucional --}}
        <div class="text-center mb-8 flex flex-col items-center">
            <img src="{{ asset('images/logo_pc.png') }}" alt="Protección Civil" class="h-20 mb-4 drop-shadow-md">
            <h2 class="text-[11px] font-black uppercase text-black leading-tight">República Bolivariana de Venezuela</h2>
            <h3 class="text-[11px] font-bold uppercase text-black leading-tight">Gobernación del Estado Bolívar</h3>
            <h4 class="text-[10px] font-bold uppercase text-black leading-tight">Secretaría de Seguridad Ciudadana</h4>
            <h4 class="text-[10px] font-bold uppercase text-black leading-tight">Dirección Estadal de Protección Civil y Administración de Desastres</h4>
            
            <div class="mt-8 border-[3px] border-black px-12 py-3 inline-block shadow-sm">
                <h1 class="text-lg font-black uppercase tracking-widest text-black">Roll de Guardia 24x72</h1>
                <h2 class="text-xs font-black uppercase text-black tracking-wider mt-1" x-text="'Técnicos de Riesgo - ' + currentMonthName + ' ' + year"></h2>
            </div>
        </div>

        {{-- Tabla del Calendario --}}
        <div class="overflow-x-auto mt-8">
            <table class="w-full border-collapse border-[2px] border-black min-w-[800px]">
                <thead>
                    <tr class="bg-gray-100 border-b-[2px] border-black">
                        @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $day)
                            <th class="py-3 border-r border-black last:border-0 text-[10px] font-black uppercase text-black tracking-widest">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-black">
                    <template x-for="(week, wIndex) in weeks" :key="wIndex">
                        <tr class="divide-x divide-black h-32">
                            <template x-for="(day, dIndex) in week" :key="day?.date || (wIndex + '-' + dIndex)">
                                <td class="p-1 align-top transition-colors relative"
                                    :class="[
                                        !day ? 'bg-gray-50' : (day.hasConflict ? 'bg-red-50/50' : 'bg-white hover:bg-orange-50/30 cursor-pointer'),
                                        day && day.hasConflict ? 'ring-inset ring-4 ring-red-500 shadow-inner' : ''
                                    ]"
                                    @click="day && day.isCurrentMonth && openModal(day)">
                                    
                                    <template x-if="day">
                                        <div class="h-full flex flex-col items-center">
                                            {{-- Número de Día y Alerta --}}
                                            <div class="w-full flex justify-between items-start px-1 pt-1">
                                                <span class="text-sm font-black text-black" :class="day.isCurrentMonth ? '' : 'opacity-40'" x-text="day.dayNumber"></span>
                                                <template x-if="day.hasConflict">
                                                    <div class="text-red-600 animate-pulse bg-red-100 px-1.5 py-0.5 rounded-md shadow-sm border border-red-200" title="¡Alerta de Regla 24x72! El técnico no ha cumplido su descanso de 72 horas.">
                                                        <i class="fas fa-exclamation-triangle text-xs"></i>
                                                    </div>
                                                </template>
                                            </div>
                                            
                                            {{-- Información de Guardia --}}
                                            <template x-if="day.duty">
                                                <div class="flex flex-col items-center justify-center flex-1 w-full" :class="day.isCurrentMonth ? '' : 'opacity-50 grayscale'">
                                                    <span class="text-3xl font-black drop-shadow-sm"
                                                          :class="{
                                                              'text-[#C1272D]': day.duty.letter === 'A',
                                                              'text-[#0B3B5E]': day.duty.letter === 'B',
                                                              'text-[#10B981]': day.duty.letter === 'C',
                                                              'text-[#F97316]': day.duty.letter === 'D'
                                                          }"
                                                          x-text="day.duty.letter"></span>
                                                    
                                                    <template x-if="day.duty.employee">
                                                        <span class="text-[9px] font-black text-gray-800 leading-none uppercase text-center mt-1 w-full truncate px-1" 
                                                              x-text="day.duty.employee.first_name.split(' ')[0] + ' ' + day.duty.employee.last_name.split(' ')[0]"></span>
                                                    </template>
                                                    <template x-if="!day.duty.employee">
                                                        <span class="text-[8px] font-bold text-gray-400 leading-none uppercase text-center mt-1 w-full">Sin Asignar</span>
                                                    </template>
                                                    
                                                    <div class="mt-auto mb-1 bg-green-100 text-green-800 px-2 py-0.5 rounded text-[8px] font-black uppercase flex items-center gap-1 border border-green-300 shadow-sm">
                                                        <i class="fas fa-check"></i> 24h
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            {{-- Indicador Hover Add --}}
                                            <div x-show="!day.duty && day.isCurrentMonth && canUpdate" class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                <i class="fas fa-plus-circle text-pc-blue text-2xl drop-shadow-lg opacity-50 hover:opacity-100"></i>
                                            </div>
                                        </div>
                                    </template>
                                </td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Notas y Leyenda Fiel --}}
        <div class="mt-8">
            <div class="border-[2px] border-black p-3 text-[9px] sm:text-[10px] font-bold text-black uppercase mb-6 shadow-sm bg-yellow-50/30">
                <strong>NOTA:</strong> LA ENTRADA A LA GUARDIA ES A LAS 0700 HLV Y LA ENTREGA DE FORMA PRESENCIAL ES A LAS 0700 HLV. NO SE DEBE RETIRAR SIN ENTREGAR LA GUARDIA.<br>
                <span class="text-green-700 mt-1 inline-block"><i class="fas fa-check"></i> 24h</span> = GUARDIA 24 HORAS
            </div>

            <div class="border-[2px] border-black p-4 text-xs text-black mb-16 shadow-sm bg-gray-50/50">
                <strong class="uppercase font-black text-xs sm:text-sm mb-3 block border-b-2 border-gray-300 pb-1">Codificación de Técnicos (Mes Actual)</strong>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-3">
                    <template x-for="L in ['A', 'B', 'C', 'D']">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 flex items-center justify-center bg-gray-200 border-2 border-black font-black text-sm rounded shadow-inner" 
                                  :class="{
                                      'text-[#C1272D]': L === 'A',
                                      'text-[#0B3B5E]': L === 'B',
                                      'text-[#10B981]': L === 'C',
                                      'text-[#F97316]': L === 'D'
                                  }"
                                  x-text="L"></span>
                            <span class="font-black uppercase text-[10px] text-gray-800" 
                                  x-text="codification[L] ? codification[L].full_name : '__________________'"></span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-around items-center gap-12 mt-20 px-4 mb-8">
                <div class="text-center w-full max-w-[250px]">
                    <div class="border-t-2 border-black mb-2 pt-2"></div>
                    <p class="text-[9px] sm:text-[10px] font-black uppercase text-black leading-tight">Director de Protección Civil y Administración de Desastres</p>
                </div>
                <div class="text-center w-full max-w-[250px]">
                    <div class="border-t-2 border-black mb-2 pt-2"></div>
                    <p class="text-[9px] sm:text-[10px] font-black uppercase text-black leading-tight">Firma del Jefe de División</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Edición (Alpine.js) --}}
    <div x-show="modalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden"
             @click.away="modalOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="bg-pc-blue px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-white font-black text-sm uppercase">Editar Guardia</h3>
                    <p class="text-blue-100 text-[10px] font-bold" x-text="selectedDay?.dateFormatted"></p>
                </div>
                <button @click="modalOpen = false" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
            </div>

            <form @submit.prevent="saveDay" class="p-6 space-y-4">
                <div>
                    <label class="label-pc text-[10px]">Letra de Guardia</label>
                    <div class="grid grid-cols-4 gap-2 mt-1">
                        <template x-for="L in ['A', 'B', 'C', 'D']">
                            <button type="button" @click="form.letter = L"
                                    :class="form.letter === L ? (L === 'A' ? 'bg-pc-red' : (L === 'B' ? 'bg-pc-blue' : (L === 'C' ? 'bg-green-500' : 'bg-pc-orange'))) + ' text-white scale-110 shadow-lg' : 'bg-gray-100 text-gray-400'"
                                    class="h-10 rounded-xl font-black transition-all flex items-center justify-center"
                                    x-text="L"></button>
                        </template>
                    </div>
                </div>

                <div class="relative">
                    <label class="label-pc text-[10px]">Personal (Opcional)</label>
                    <div class="relative flex items-center">
                        <input type="text"
                               x-model="autocompleteSearch"
                               @input.debounce.300ms="fetchAutocompleteResults()"
                               @focus="autocompleteOpen = true"
                               @click.away="setTimeout(() => { if (!form.employee_id) autocompleteSearch = ''; autocompleteOpen = false; }, 200)"
                               placeholder="Buscar por nombre o cédula..."
                               class="w-full rounded-md border border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 text-xs pr-8 py-2 px-3">
                        
                        <div class="absolute right-2.5 flex items-center space-x-1">
                            <template x-if="autocompleteLoading">
                                <i class="fas fa-spinner animate-spin text-gray-400 text-[10px]"></i>
                            </template>
                            <template x-if="!autocompleteLoading && autocompleteSearch">
                                <button type="button" @click="clearAutocomplete()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </template>
                            <template x-if="!autocompleteLoading && !autocompleteSearch">
                                <i class="fas fa-search text-gray-400 text-[10px]"></i>
                            </template>
                        </div>
                    </div>
                    
                    <input type="hidden" name="employee_id" x-model="form.employee_id">

                    <div x-show="autocompleteOpen && autocompleteResults.length > 0"
                         x-cloak
                         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto">
                        <ul class="py-1 text-[11px] text-gray-700 font-medium">
                            <template x-for="item in autocompleteResults" :key="item.id">
                                <li>
                                    <button type="button"
                                            @click="selectAutocompleteItem(item)"
                                            class="w-full text-left px-3 py-2 hover:bg-pc-orange hover:text-white transition-colors">
                                        <span x-text="item.label"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="label-pc text-[10px]">Observaciones</label>
                    <input type="text" x-model="form.notes" class="input-pc text-xs" placeholder="Ej: Suplencia, Cambio...">
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="button" @click="modalOpen = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-500 font-bold rounded-xl text-xs uppercase">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-pc-blue text-white font-black rounded-xl text-xs uppercase shadow-lg shadow-blue-100">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        const canUpdate = document.querySelector('[data-can-update]')?.dataset.canUpdate === '1';

        Alpine.data('guardCalendar', (rotationId, initialYear, initialMonth) => ({
            rotationId: rotationId,
            year: initialYear,
            month: initialMonth,
            weeks: [],
            duties: {},
            modalOpen: false,
            selectedDay: null,
            canUpdate: canUpdate,
            form: { letter: 'A', employee_id: '', notes: '' },
            currentMonthName: '',
            conflictingDates: new Set(),
            codification: {
                A: @json($guardRotation->employeeA),
                B: @json($guardRotation->employeeB),
                C: @json($guardRotation->employeeC),
                D: @json($guardRotation->employeeD)
            },
            autocompleteOpen: false,
            autocompleteSearch: '',
            autocompleteResults: [],
            autocompleteLoading: false,
            employeesMap: {
                @foreach($employees as $emp)
                    '{{ $emp->id }}': {
                        id_number: '{{ $emp->id_number }}',
                        first_name: '{{ $emp->first_name }}',
                        last_name: '{{ $emp->last_name }}'
                    },
                @endforeach
            },

            async init() {
                this.updateMonthName();
                await this.fetchDuties();
                this.buildCalendar();

                this.$watch('modalOpen', (value) => {
                    if (value) {
                        const val = this.form.employee_id;
                        if (!val) {
                            this.autocompleteSearch = '';
                        } else {
                            const emp = this.employeesMap[val];
                            if (emp) {
                                this.autocompleteSearch = emp.id_number + ' - ' + emp.first_name + ' ' + emp.last_name;
                            } else {
                                this.autocompleteSearch = '';
                            }
                        }
                    }
                });
            },

            fetchAutocompleteResults() {
                if (this.autocompleteSearch.length < 2) {
                    this.autocompleteResults = [];
                    this.autocompleteOpen = false;
                    return;
                }
                this.autocompleteLoading = true;
                fetch('{{ route("api.employees.autocomplete") }}?term=' + encodeURIComponent(this.autocompleteSearch))
                    .then(res => res.json())
                    .then(data => {
                        this.autocompleteResults = data;
                        this.autocompleteLoading = false;
                        this.autocompleteOpen = true;
                    })
                    .catch(() => { this.autocompleteLoading = false; });
            },

            selectAutocompleteItem(item) {
                this.form.employee_id = item.id;
                this.autocompleteSearch = item.label;
                this.autocompleteOpen = false;
            },

            clearAutocomplete() {
                this.form.employee_id = '';
                this.autocompleteSearch = '';
                this.autocompleteResults = [];
                this.autocompleteOpen = false;
            },

            updateMonthName() {
                this.currentMonthName = new Date(this.year, this.month - 1).toLocaleDateString('es-ES', { month: 'long' });
            },

            async fetchDuties() {
                try {
                    const response = await fetch(`{{ url('/guard-rotations') }}/${this.rotationId}/data?month=${this.month}&year=${this.year}`);
                    this.duties = await response.json();
                    this.checkConflicts();
                } catch (e) { console.error(e); }
            },

            checkConflicts() {
                this.conflictingDates = new Set();
                this.codification = {
                    A: @json($guardRotation->employeeA),
                    B: @json($guardRotation->employeeB),
                    C: @json($guardRotation->employeeC),
                    D: @json($guardRotation->employeeD)
                };
                
                let employeeDates = {};
                for (let dateStr in this.duties) {
                    const duty = this.duties[dateStr];
                    if (duty.employee) {
                        this.codification[duty.letter] = duty.employee;
                        
                        if (!employeeDates[duty.employee_id]) employeeDates[duty.employee_id] = [];
                        employeeDates[duty.employee_id].push(new Date(dateStr));
                    }
                }
                
                for (let empId in employeeDates) {
                    let dates = employeeDates[empId].sort((a,b) => a - b);
                    for (let i = 0; i < dates.length - 1; i++) {
                        let diffTime = dates[i+1] - dates[i];
                        let diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                        // Regla 24x72: Trabaja 1 día, descansa 3. El próximo trabajo válido es al 4to día. (diffDays >= 4)
                        if (diffDays > 0 && diffDays < 4) {
                            this.conflictingDates.add(dates[i].toLocaleDateString('en-CA'));
                            this.conflictingDates.add(dates[i+1].toLocaleDateString('en-CA'));
                        }
                    }
                }
            },

            buildCalendar() {
                const firstDay = new Date(this.year, this.month - 1, 1);
                const lastDay = new Date(this.year, this.month, 0);
                
                let startDate = new Date(firstDay);
                const dayOfWeek = firstDay.getDay(); // 0 is Sunday
                const offset = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Adjust to Monday start
                startDate.setDate(1 - offset);

                const weeks = [];
                let currentDate = new Date(startDate);
                const todayStr = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD

                for (let w = 0; w < 6; w++) {
                    const week = [];
                    for (let d = 0; d < 7; d++) {
                        const dateStr = currentDate.toLocaleDateString('en-CA');
                        const duty = this.duties[dateStr] || null;
                        
                        week.push({
                            date: dateStr,
                            dayNumber: currentDate.getDate(),
                            isCurrentMonth: currentDate.getMonth() === (this.month - 1),
                            isToday: dateStr === todayStr,
                            duty: duty,
                            hasConflict: this.conflictingDates.has(dateStr),
                            dateFormatted: currentDate.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' })
                        });
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                    weeks.push(week);
                    if (currentDate > lastDay && currentDate.getMonth() !== (this.month - 1)) break;
                }
                this.weeks = weeks;
            },

            async changeMonth(delta) {
                this.month += delta;
                if (this.month < 1) { this.month = 12; this.year--; }
                else if (this.month > 12) { this.month = 1; this.year++; }
                
                this.updateMonthName();
                await this.fetchDuties();
                this.buildCalendar();
                window.history.replaceState({}, '', `?month=${this.month}&year=${this.year}`);
            },

            openModal(day) {
                if (!this.canUpdate) return;
                this.selectedDay = day;
                this.form = {
                    letter: day.duty?.letter || 'A',
                    employee_id: day.duty?.employee_id || '',
                    notes: day.duty?.notes || ''
                };
                this.modalOpen = true;
            },

            async saveDay() {
                if (this.form.employee_id) {
                    let employeeAssignedToLetter = null;
                    for (let L in this.codification) {
                        if (this.codification[L] && this.codification[L].id == this.form.employee_id) {
                            employeeAssignedToLetter = L;
                            break;
                        }
                    }
                    if (employeeAssignedToLetter && employeeAssignedToLetter !== this.form.letter) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Asignación Inválida',
                            text: `Este técnico ya está cubriendo la guardia "${employeeAssignedToLetter}" durante este mes. No puede asignarse a la guardia "${this.form.letter}".`,
                            confirmButtonColor: '#C1272D'
                        });
                        return;
                    }
                }
                
                let applyToAll = false;
                if (this.form.employee_id) {
                    const result = await Swal.fire({
                        title: '¿Aplicar al mes completo?',
                        text: `¿Desea asignar a este técnico a todos los días del mes que tengan la letra "${this.form.letter}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0B3B5E',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Sí, aplicar a todos',
                        cancelButtonText: 'No, solo este día'
                    });
                    applyToAll = result.isConfirmed;
                }

                try {
                    const payload = {
                        date: this.selectedDay.date,
                        apply_to_all: applyToAll,
                        ...this.form
                    };
                    
                    const response = await fetch(`{{ url('/guard-rotations') }}/${this.rotationId}/update-day`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        
                        if (applyToAll) {
                            await this.fetchDuties();
                        } else {
                            this.duties[this.selectedDay.date] = result.duty;
                            this.checkConflicts();
                        }
                        
                        this.buildCalendar();
                        this.modalOpen = false;
                        
                        if (this.conflictingDates.has(this.selectedDay.date)) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Regla 24x72 Incumplida',
                                text: 'El técnico asignado no tiene las 72 horas de descanso reglamentarias. Se ha marcado en rojo.',
                                confirmButtonColor: '#F97316'
                            });
                        }
                    }
                } catch (e) { console.error(e); }
            }
        }));
    });
</script>
@endpush