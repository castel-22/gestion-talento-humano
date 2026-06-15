@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-pc-blue">Nueva Rotación de Guardia</h2>
            </div>

            @if($errors->any() || session('error'))
            <div class="m-6 mb-0 bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500"></i></div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-700">Ocurrieron los siguientes errores:</h3>
                        <ul class="mt-1 list-disc list-inside text-xs text-red-600 font-medium">
                            @if(session('error')) <li>{{ session('error') }}</li> @endif
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('guard-rotations.store') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la rotación <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                           placeholder="Ej: TÉCNICOS DE RIESGO" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50">{{ old('description') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pc-orange shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Rotación activa</span>
                    </label>
                </div>

                <!-- Codificación de Técnicos -->
                <div class="border-t border-gray-200 pt-6 mt-6 mb-6">
                    <h3 class="text-sm font-black uppercase text-pc-blue mb-2">Codificación de Técnicos (Rol 24x72)</h3>
                    <p class="text-xs text-gray-500 mb-4">Asigne un técnico a cada letra de la guardia. Al generar la secuencia mensual, el sistema asignará automáticamente a estos trabajadores.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $letter)
                            <div x-data="{
                                open: false,
                                search: '{{ old("employee_{$key}_id") ? ($employees->firstWhere('id', old("employee_{$key}_id"))?->id_number . ' - ' . $employees->firstWhere('id', old("employee_{$key}_id"))?->full_name) : '' }}',
                                selectedId: '{{ old("employee_{$key}_id") }}',
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
                            }" class="relative">
                                <label for="employee_{{ $key }}_id" class="block text-xs font-bold text-gray-700 uppercase mb-1">
                                    Guardia {{ $letter }}
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text"
                                           x-model="search"
                                           @input.debounce.300ms="fetchResults()"
                                           @focus="open = true"
                                           @click.away="setTimeout(() => { if (!selectedId) search = ''; open = false; }, 200)"
                                           placeholder="Buscar por nombre o cédula..."
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 text-xs pr-8">
                                    
                                    <div class="absolute right-2.5 flex items-center space-x-1">
                                        <template x-if="loading">
                                            <i class="fas fa-spinner animate-spin text-gray-400 text-[10px]"></i>
                                        </template>
                                        <template x-if="!loading && search">
                                            <button type="button" @click="clear()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <i class="fas fa-times text-[10px]"></i>
                                            </button>
                                        </template>
                                        <template x-if="!loading && !search">
                                            <i class="fas fa-search text-gray-400 text-[10px]"></i>
                                        </template>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="employee_{{ $key }}_id" id="employee_{{ $key }}_id" x-model="selectedId">

                                <div x-show="open && results.length > 0"
                                     x-cloak
                                     class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <ul class="py-1 text-[11px] text-gray-700 dark:text-gray-300">
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

                                @error("employee_{$key}_id")
                                    <p class="mt-1 text-[10px] text-red-600 uppercase font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('guard-rotations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-pc-blue hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection