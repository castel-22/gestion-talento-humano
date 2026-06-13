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
                <span class="text-sm text-pc-orange font-medium">Departamentos</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-pc-blue uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-sitemap text-pc-orange"></i> Unidades Organizativas
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Estructura interna de Protección Civil Bolívar</p>
        </div>
        @can('create', App\Models\Department::class)
            <a href="{{ route('departments.create') }}" class="bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Nueva Unidad
            </a>
        @endcan
    </div>

    {{-- Buscador Premium --}}
    <div class="mb-10">
        <form method="GET" action="{{ route('departments.index') }}" id="searchForm" class="relative group">
            <div class="absolute z-10 inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-300 group-focus-within:text-pc-orange transition-colors"></i>
            </div>
            <input type="text" id="search_input" name="search" value="{{ request('search') }}" 
                   placeholder="Buscar unidad operativa o administrativa..." 
                   class="input-pc pl-14 py-4 bg-white shadow-xl shadow-gray-100 border-none text-xs font-bold">
            <div id="suggestions" class="absolute z-50 bg-white border border-gray-100 rounded-2xl w-full hidden shadow-2xl mt-2 max-h-60 overflow-auto"></div>
            <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'id_desc') }}">
        </form>
    </div>

    {{-- Cuadrícula de Departamentos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($departments as $dept)
            <div class="card-pc group hover:-translate-y-2 transition-all duration-300 relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity pointer-events-none">
                    <i class="fas fa-sitemap text-8xl"></i>
                </div>
                
                <div class="p-8 flex-1">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-pc-blue/5 border border-pc-blue/10 flex items-center justify-center overflow-hidden shadow-inner">
                            @if($dept->logo)
                                <img src="{{ Storage::url($dept->logo) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-building text-pc-blue text-xl"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-black text-pc-blue uppercase tracking-tight truncate">{{ $dept->name }}</h3>
                            <p class="text-[9px] font-black text-pc-orange uppercase tracking-widest mt-1">ID Unidad: #{{ $dept->id }}</p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 font-medium leading-relaxed line-clamp-3 mb-6">
                        {{ $dept->description ?: 'Sin descripción detallada de funciones para esta unidad.' }}
                    </p>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                        <div class="flex flex-col">
                            <span class="text-[20px] font-black text-pc-blue leading-none">{{ $dept->employees_count }}</span>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-tighter mt-1">Integrantes</span>
                        </div>
                        <div class="flex gap-2">
                            @can('view', $dept)
                                <a href="{{ route('departments.show', $dept) }}" class="w-9 h-9 rounded-xl bg-gray-50 text-pc-blue flex items-center justify-center hover:bg-pc-blue hover:text-white transition-all shadow-sm border border-gray-100">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            @endcan
                            @can('update', $dept)
                                <a href="{{ route('departments.edit', $dept) }}" class="w-9 h-9 rounded-xl bg-gray-50 text-pc-orange flex items-center justify-center hover:bg-pc-orange hover:text-white transition-all shadow-sm border border-gray-100">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                @can('delete', $dept)
                    <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="absolute top-4 right-4 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity confirm-delete" data-label="{{ $dept->name }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-pc-red hover:bg-pc-red/10 p-2 rounded-lg transition-colors">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                @endcan
            </div>
        @endforeach
    </div>

    <div class="mt-12">
        {{ $departments->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('search_input');
    const suggestionsDiv = document.getElementById('suggestions');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const term = this.value.trim();
        if (term.length < 2) { suggestionsDiv.classList.add('hidden'); return; }
        debounceTimer = setTimeout(() => {
            fetch(`{{ route('departments.autocomplete') }}?term=${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) { suggestionsDiv.classList.add('hidden'); return; }
                    suggestionsDiv.innerHTML = data.map(item => `
                        <div class="suggestion-item px-6 py-4 hover:bg-pc-blue/5 cursor-pointer border-b border-gray-50 flex items-center gap-3 transition-colors" data-value="${item.value}">
                            <i class="fas fa-sitemap text-gray-300"></i>
                            <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">${item.label}</span>
                        </div>
                    `).join('');
                    suggestionsDiv.classList.remove('hidden');
                });
        }, 300);
    });

    suggestionsDiv.addEventListener('click', (e) => {
        const target = e.target.closest('.suggestion-item');
        if (target) {
            searchInput.value = target.getAttribute('data-value');
            suggestionsDiv.classList.add('hidden');
            document.getElementById('searchForm').submit();
        }
    });
</script>
@endpush