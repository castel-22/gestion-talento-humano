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
                <span class="text-sm text-pc-orange font-medium">Calendario Institucional</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="card-pc p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-pc-blue flex items-center gap-3">
                    <i class="fas fa-calendar-alt text-pc-orange"></i> Planificación de Vacaciones
                </h2>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-widest mt-1">Calendario de disponibilidad y contingencias</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex items-center gap-2 bg-pc-blue/5 px-3 py-1.5 rounded-lg border border-pc-blue/10">
                    <span class="w-3 h-3 rounded-full bg-[#0B3B5E]"></span>
                    <span class="text-[10px] font-bold text-pc-blue uppercase">Aprobadas</span>
                </div>
                <div class="flex items-center gap-2 bg-pc-orange/5 px-3 py-1.5 rounded-lg border border-pc-orange/10">
                    <span class="w-3 h-3 rounded-full bg-[#F97316]"></span>
                    <span class="text-[10px] font-bold text-pc-orange uppercase">En Curso</span>
                </div>
                <div class="flex items-center gap-2 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100">
                    <span class="w-3 h-3 rounded-full bg-[#E63946]"></span>
                    <span class="text-[10px] font-bold text-pc-red uppercase">Contingencia</span>
                </div>
            </div>
        </div>

        <div id="calendar" class="min-h-[700px] bg-white rounded-xl"></div>
    </div>
</div>

{{-- Modal para detalles del evento --}}
<div x-data="{ open: false, title: '', start: '', end: '', url: '', type: '' }" 
     @open-event-modal.window="open = true; title = $event.detail.title; start = $event.detail.start; end = $event.detail.end; url = $event.detail.url; type = $event.detail.type"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all"
         @click.away="open = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div :class="type === 'plan' ? 'bg-pc-red' : 'bg-pc-blue'" class="px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold" x-text="type === 'plan' ? 'Plan de Contingencia' : 'Detalles de Vacaciones'"></h3>
            <button @click="open = false" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-4 mb-6">
                <div :class="type === 'plan' ? 'bg-red-100 text-red-600' : 'bg-pc-blue/10 text-pc-blue'" class="p-3 rounded-xl">
                    <i :class="type === 'plan' ? 'fas fa-exclamation-triangle' : 'fas fa-umbrella-beach'" class="text-2xl"></i>
                </div>
                <div>
                    <p class="text-lg font-black text-gray-800" x-text="title"></p>
                    <p class="text-xs text-gray-500 uppercase tracking-widest font-bold" x-text="type === 'plan' ? 'Restricción de salida' : 'Personal en periodo vacacional'"></p>
                </div>
            </div>
            
            <div class="space-y-3 mb-8">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                    <span class="text-xs font-bold text-gray-400 uppercase">Desde</span>
                    <span class="text-sm font-black text-pc-blue" x-text="start"></span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                    <span class="text-xs font-bold text-gray-400 uppercase">Hasta</span>
                    <span class="text-sm font-black text-pc-blue" x-text="end"></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="open = false" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-colors">
                    Cerrar
                </button>
                <template x-if="url">
                    <a :href="url" class="flex-1 px-4 py-3 bg-pc-orange hover:bg-orange-600 text-white font-bold rounded-xl text-center shadow-lg shadow-orange-200 transition-all">
                        Ver Expediente
                    </a>
                </template>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                list: 'Listado'
            },
            events: '{{ route('vacations.events') }}',
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                
                const type = info.event.id.split('_')[0];
                const id = info.event.id.split('_')[1];
                
                window.dispatchEvent(new CustomEvent('open-event-modal', {
                    detail: {
                        title: info.event.title,
                        start: info.event.start.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' }),
                        end: info.event.end ? new Date(info.event.end.getTime() - 86400000).toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' }) : '',
                        url: info.event.url,
                        type: type
                    }
                }));
            },
            eventDidMount: function(info) {
                // Tooltip simple o mejoras visuales
                if (info.event.display === 'background') {
                    info.el.title = "Plan de Contingencia: No se permiten nuevas vacaciones en este periodo";
                }
            }
        });
        calendar.render();
    });
</script>

<style>
    .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9; }
    .fc-header-toolbar { margin-bottom: 2rem !important; }
    .fc-toolbar-title { font-weight: 800 !important; color: #0B3B5E !important; text-transform: uppercase; letter-spacing: -0.025em; font-size: 1.25rem !important; }
    .fc-button-primary { background-color: #0B3B5E !important; border-color: #0B3B5E !important; font-weight: 700 !important; text-transform: uppercase; font-size: 0.65rem !important; padding: 0.5rem 1rem !important; border-radius: 0.75rem !important; transition: all 0.2s !important; }
    .fc-button-primary:hover { background-color: #F97316 !important; border-color: #F97316 !important; transform: translateY(-1px); }
    .fc-button-active { background-color: #F97316 !important; border-color: #F97316 !important; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2) !important; }
    .fc-daygrid-day-number { font-weight: 800; color: #64748b; font-size: 0.75rem; padding: 8px !important; }
    .fc-col-header-cell { background-color: #f8fafc; padding: 12px 0 !important; }
    .fc-col-header-cell-cushion { color: #0B3B5E; font-weight: 800; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.05em; }
    .fc-event { border-radius: 6px !important; border: none !important; padding: 2px 4px !important; font-weight: 700 !important; font-size: 0.7rem !important; box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important; cursor: pointer !important; }
    .fc-day-today { background-color: rgba(249, 115, 22, 0.03) !important; }
    .fc-day-today .fc-daygrid-day-number { color: #F97316; background-color: rgba(249, 115, 22, 0.1); border-radius: 4px; }
</style>
@endpush