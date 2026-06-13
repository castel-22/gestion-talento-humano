@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-picker { height: 300px; width: 100%; border-radius: 12px; z-index: 1; border: 2px solid #e2e8f0; }
    .dark #map-picker { border-color: #1e293b; }
    .leaflet-layer, .leaflet-control-zoom-in, .leaflet-control-zoom-out, .leaflet-control-attribution {
        filter: var(--map-filter, none);
    }
    .dark { --map-filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%); }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

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
                <a href="{{ route('deployments.index') }}" class="text-sm text-gray-700 hover:text-pc-orange">Despliegues</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Nuevo Despliegue</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="deploymentForm()" class="max-w-6xl mx-auto">
    <div class="card-pc">
        {{-- Encabezado --}}
        <div class="bg-pc-blue px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <i class="fas fa-truck-loading text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white uppercase tracking-tight">Nuevo Despliegue Operativo</h2>
                    <p class="text-blue-100 text-xs">Asignación de misión y personal en tiempo real</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('deployments.index') }}" class="text-white/80 hover:text-white transition-colors text-sm font-medium flex items-center">
                    Cancelar
                </a>
                <button type="button" @click="validateAndSubmit"
                        class="bg-pc-orange hover:bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg transition-all transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Despliegue
                </button>
            </div>
        </div>

        <form id="deployment-form" action="{{ route('deployments.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">
                
                {{-- COLUMNA IZQUIERDA: Misión y Logística --}}
                <div class="p-8 space-y-6 bg-gray-50/30">
                    <div class="space-y-5">
                        <h3 class="text-sm font-bold text-pc-blue uppercase tracking-widest flex items-center gap-2 mb-4">
                            <span class="bg-pc-blue/10 p-1.5 rounded-full"><i class="fas fa-info-circle text-pc-blue text-xs"></i></span>
                            Información de la Misión
                        </h3>

                        <div>
                            <label for="place" class="label-pc">Lugar del despliegue <span class="text-pc-red">*</span></label>
                            <input type="text" name="place" id="place" required placeholder="Ej: Jardín Botánico, Ciudad Bolívar"
                                   class="input-pc @error('place') border-red-500 @enderror" value="{{ old('place') }}">
                            @error('place') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="label-pc">Latitud (opcional)</label>
                                <input type="text" name="latitude" id="latitude" x-model="latitude" @input="updateMapFromInputs" placeholder="8.1234" class="input-pc" value="{{ old('latitude') }}">
                            </div>
                            <div>
                                <label for="longitude" class="label-pc">Longitud (opcional)</label>
                                <input type="text" name="longitude" id="longitude" x-model="longitude" @input="updateMapFromInputs" placeholder="-63.1234" class="input-pc" value="{{ old('longitude') }}">
                            </div>
                        </div>

                        {{-- Selector de Mapa --}}
                        <div class="space-y-2">
                            <label class="label-pc text-[10px] flex justify-between items-center">
                                <span>Selector Geográfico</span>
                                <span class="text-pc-orange font-black uppercase italic">Clic para marcar ubicación</span>
                            </label>
                            <div class="relative">
                                <div id="map-picker" class="skeleton"></div>
                                {{-- Buscador de Ubicación --}}
                                <div class="absolute top-2 left-12 z-[1000] w-64">
                                    <div class="relative">
                                        <input type="text" id="map-search" placeholder="Buscar dirección o zona..." 
                                               class="w-full text-[10px] py-2 pl-8 pr-4 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-lg shadow-xl border-0 focus:ring-2 focus:ring-pc-orange outline-none">
                                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="division" class="label-pc">División a cargo</label>
                            <input type="text" name="division" id="division" placeholder="Ej: Operaciones, Rescate, Gestión de Riesgo"
                                   class="input-pc" value="{{ old('division') }}">
                        </div>

                        <div>
                            <label for="reason" class="label-pc">Motivo del despliegue <span class="text-pc-red">*</span></label>
                            <textarea name="reason" id="reason" rows="3" required placeholder="Describa el objetivo y motivo de la misión..."
                                      class="input-pc @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                            @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_datetime" class="label-pc">Inicio <span class="text-pc-red">*</span></label>
                                <input type="datetime-local" name="start_datetime" id="start_datetime" required
                                       class="input-pc @error('start_datetime') border-red-500 @enderror" value="{{ old('start_datetime') }}">
                            </div>
                            <div>
                                <label for="end_datetime" class="label-pc">Fin proyectado</label>
                                <input type="datetime-local" name="end_datetime" id="end_datetime" 
                                       :disabled="isIndefinite"
                                       :class="isIndefinite ? 'bg-gray-100 opacity-50 cursor-not-allowed' : ''"
                                       class="input-pc" value="{{ old('end_datetime') }}">
                            </div>
                        </div>

                        <div class="flex items-center">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" name="is_indefinite" x-model="isIndefinite" class="rounded border-gray-300 text-pc-orange shadow-sm focus:ring-pc-orange">
                                <span class="ml-2 text-sm text-gray-600 font-medium group-hover:text-pc-orange transition-colors">Duración indefinida (sin fecha de fin)</span>
                            </label>
                        </div>

                        <div>
                            <label for="supervisor_id" class="label-pc">Supervisor / Responsable <span class="text-pc-red">*</span></label>
                            <select name="supervisor_id" id="supervisor_id" required class="input-pc">
                                <option value="">Seleccione un responsable...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('supervisor_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->id_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: Personal Asignado --}}
                <div class="p-8 flex flex-col bg-white">
                    <h3 class="text-sm font-bold text-pc-blue uppercase tracking-widest flex items-center gap-2 mb-6">
                        <span class="bg-pc-blue/10 p-1.5 rounded-full"><i class="fas fa-users text-pc-blue text-xs"></i></span>
                        Personal Asignado
                        <span class="ml-auto bg-pc-orange/10 text-pc-orange text-[10px] font-black px-2 py-0.5 rounded-full" x-text="selectedParticipants.length"></span>
                    </h3>

                    {{-- Buscador AJAX --}}
                    <div class="relative mb-6">
                        <div class="absolute z-10 inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" x-model="searchTerm" @input.debounce.300ms="searchEmployees"
                               placeholder="Buscar personal por nombre o cédula..."
                               class="input-pc pl-10 h-12 shadow-inner border-gray-200 focus:ring-pc-blue focus:border-pc-blue">
                        
                        {{-- Spinner --}}
                        <div x-show="isLoading" class="absolute z-10 inset-y-0 right-3 flex items-center" x-cloak>
                            <svg class="animate-spin h-5 w-5 text-pc-orange" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        {{-- Sugerencias --}}
                        <div x-show="suggestions.length > 0" x-cloak
                             @click.away="suggestions = []"
                             class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto">
                            <template x-for="emp in suggestions" :key="emp.id">
                                <button type="button" @click="addParticipant(emp)"
                                        class="w-full px-4 py-3 hover:bg-pc-orange/5 text-left border-b border-gray-50 last:border-0 flex justify-between items-center group transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-pc-blue/10 flex items-center justify-center text-pc-blue font-bold text-xs group-hover:bg-pc-orange group-hover:text-white transition-all">
                                            <span x-text="emp.label.split(' ').filter(n => n && !n.includes('-')).map(n => n[0]).join('').substring(0,2).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-800" x-text="emp.label.split(' - ')[1]"></div>
                                            <div class="text-[10px] text-gray-500 font-medium" x-text="'CI: ' + emp.id_number"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-plus-circle text-pc-orange opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Lista de participantes seleccionados --}}
                    <div class="flex-1 overflow-y-auto space-y-4 pr-2 min-h-[300px]">
                        <template x-for="(p, index) in selectedParticipants" :key="p.id">
                            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                                <div class="absolute left-0 top-0 w-1 h-full bg-pc-blue group-hover:bg-pc-orange transition-colors"></div>
                                
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-pc-blue text-white flex items-center justify-center text-xs font-black shadow-inner">
                                            <span x-text="p.full_name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900" x-text="p.full_name"></div>
                                            <div class="text-[10px] text-pc-blue font-bold" x-text="'CI: ' + p.id_number"></div>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeParticipant(index)" class="text-gray-300 hover:text-pc-red transition-colors p-1">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Rol en Misión</label>
                                        <select x-model="p.role" class="w-full border-gray-200 rounded-lg text-xs mt-1 focus:ring-pc-orange focus:border-pc-orange">
                                            <option value="">Seleccionar rol...</option>
                                            <option value="Rescatista">Rescatista</option>
                                            <option value="Evaluador">Evaluador</option>
                                            <option value="Paramédico">Paramédico</option>
                                            <option value="Logística">Logística</option>
                                            <option value="Comunicaciones">Comunicaciones</option>
                                            <option value="Conductor">Conductor</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-2">Responsabilidad</label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="p.is_leader" class="rounded border-gray-300 text-pc-orange shadow-sm focus:ring-pc-orange">
                                            <span class="text-xs font-bold text-gray-600">Líder de Grupo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="selectedParticipants.length === 0" class="flex flex-col items-center justify-center py-16 border-2 border-dashed border-gray-100 rounded-2xl text-gray-300">
                            <i class="fas fa-user-plus text-5xl mb-4 opacity-20"></i>
                            <p class="text-sm font-medium">No se han asignado participantes</p>
                            <p class="text-[10px] mt-1">Busque personal en el cuadro de arriba</p>
                        </div>
                    </div>

                    {{-- Error de validación client-side --}}
                    <div x-show="participantError" x-cloak class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg flex items-center gap-2 text-red-600 text-xs font-bold">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span x-text="participantError"></span>
                    </div>
                </div>
            </div>

            {{-- Sección de Notas --}}
            <div class="p-8 border-t border-gray-100 bg-gray-50/20">
                <label for="notes" class="label-pc">Notas adicionales de la Operación</label>
                <textarea name="notes" id="notes" rows="2" placeholder="Información técnica, equipo especial requerido, contactos locales, etc."
                          class="input-pc">{{ old('notes') }}</textarea>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function deploymentForm() {
        return {
            isIndefinite: false,
            searchTerm: '',
            isLoading: false,
            suggestions: [],
            selectedParticipants: [],
            participantError: '',
            latitude: '{{ old('latitude') }}',
            longitude: '{{ old('longitude') }}',
            map: null,
            marker: null,

            init() {
                this.initMap();
            },

            initMap() {
                setTimeout(() => {
                    this.map = L.map('map-picker').setView([8.1283, -63.5497], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                    
                    document.getElementById('map-picker').classList.remove('skeleton');

                    // Si ya hay coordenadas, poner marcador
                    if (this.latitude && this.longitude) {
                        this.marker = L.marker([this.latitude, this.longitude]).addTo(this.map);
                        this.map.setView([this.latitude, this.longitude], 15);
                    }

                    this.map.on('click', (e) => {
                        const { lat, lng } = e.latlng;
                        this.latitude = lat.toFixed(6);
                        this.longitude = lng.toFixed(6);
                        
                        if (this.marker) {
                            this.marker.setLatLng(e.latlng);
                        } else {
                            this.marker = L.marker(e.latlng).addTo(this.map);
                        }
                    });

                    // Buscador de mapa simple (Nominatim)
                    const searchInput = document.getElementById('map-search');
                    searchInput.addEventListener('keypress', async (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const query = searchInput.value;
                            if (query.length < 3) return;

                            try {
                                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&viewbox=-64.0,8.5,-63.0,7.5`);
                                const data = await res.json();
                                if (data.length > 0) {
                                    const loc = data[0];
                                    const coords = [parseFloat(loc.lat), parseFloat(loc.lon)];
                                    this.map.setView(coords, 15);
                                    this.latitude = coords[0].toFixed(6);
                                    this.longitude = coords[1].toFixed(6);
                                    if (this.marker) this.marker.setLatLng(coords);
                                    else this.marker = L.marker(coords).addTo(this.map);
                                }
                            } catch (err) { console.error(err); }
                        }
                    });
                }, 500);
            },
            
            updateMapFromInputs() {
                const lat = parseFloat(this.latitude);
                const lng = parseFloat(this.longitude);
                if (!isNaN(lat) && !isNaN(lng) && this.map) {
                    const coords = [lat, lng];
                    if (this.marker) {
                        this.marker.setLatLng(coords);
                    } else {
                        this.marker = L.marker(coords).addTo(this.map);
                    }
                    this.map.setView(coords, 15);
                }
            },
            
            async searchEmployees() {
                if (this.searchTerm.length < 2) {
                    this.suggestions = [];
                    return;
                }
                
                this.isLoading = true;
                try {
                    const response = await fetch(`{{ url('/api/employees/autocomplete') }}?term=${encodeURIComponent(this.searchTerm)}`);
                    if (response.ok) {
                        const data = await response.json();
                        this.suggestions = data.map(e => ({
                            id: e.id,
                            label: e.label,
                            id_number: e.value
                        }));
                    }
                } catch (error) {
                    console.error('Error al buscar empleados:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            
            addParticipant(emp) {
                if (!this.selectedParticipants.find(p => p.id === emp.id)) {
                    this.selectedParticipants.push({
                        id: emp.id,
                        full_name: emp.label.split(' - ')[1],
                        id_number: emp.id_number,
                        role: '',
                        division: '',
                        is_leader: false
                    });
                    this.participantError = '';
                }
                this.searchTerm = '';
                this.suggestions = [];
            },
            
            removeParticipant(index) {
                this.selectedParticipants.splice(index, 1);
            },
            
            validateAndSubmit() {
                if (this.selectedParticipants.length === 0) {
                    this.participantError = 'Debe asignar al menos un participante al despliegue.';
                    // Scroll to error
                    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                    return;
                }
                
                this.participantError = '';
                const form = document.getElementById('deployment-form');
                
                // Limpiar inputs previos si existieran
                form.querySelectorAll('input[name^="participants["]').forEach(el => el.remove());
                
                // Inyectar datos de participantes
                this.selectedParticipants.forEach(p => {
                    const baseName = `participants[${p.id}]`;
                    
                    const data = {
                        'employee_id': p.id,
                        'role': p.role || '',
                        'division': p.division || '',
                        'is_leader': p.is_leader ? '1' : '0'
                    };
                    
                    for (const [key, value] of Object.entries(data)) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `${baseName}[${key}]`;
                        input.value = value;
                        form.appendChild(input);
                    }
                });
                
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection