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
                <span class="text-sm text-pc-orange font-medium">Gestión de Usuarios</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="card-pc p-6 dark:bg-slate-900 dark:border-slate-800">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h2 class="text-xl font-black text-pc-blue dark:text-white uppercase tracking-tight flex items-center gap-3">
                    <i class="fas fa-users-cog text-pc-orange"></i> Control de Operadores
                </h2>
                <p class="text-gray-500 dark:text-gray-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-1 opacity-70">Gestión de Seguridad de Cuentas Institucionales</p>
            </div>
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="btn-pc-secondary px-8 py-3 shadow-xl shadow-blue-900/20 active:scale-95">
                    <i class="fas fa-user-plus"></i> Registrar Operador
                </a>
            @endcan
        </div>

        {{-- Buscador y Filtros --}}
        <form method="GET" action="{{ route('users.index') }}" id="searchForm" class="mb-10">
            <div class="relative group">
                <div class="absolute z-10 inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 dark:text-slate-600 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input type="text" id="search_input" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar por nombre o correo táctico..." 
                       class="input-pc pl-14 h-14 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 transition-all shadow-inner">
                <div id="suggestions" class="absolute z-50 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-2xl w-full hidden shadow-2xl mt-2 max-h-60 overflow-auto"></div>
            </div>
            <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'id_desc') }}">
        </form>

        <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
            <table class="w-full text-sm text-left table-pc">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-5 text-[9px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-[0.2em] cursor-pointer" data-sort="id">
                            ID <i class="fas fa-sort ml-1 opacity-30"></i>
                        </th>
                        <th class="px-6 py-5 text-[9px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-[0.2em] cursor-pointer" data-sort="name">
                            Operador <i class="fas fa-sort ml-1 opacity-30"></i>
                        </th>
                        <th class="px-6 py-5 text-[9px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-[0.2em] cursor-pointer" data-sort="email">
                            Correo <i class="fas fa-sort ml-1 opacity-30"></i>
                        </th>
                        <th class="px-6 py-5 text-[9px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-[0.2em]">Rango / Rol</th>
                        <th class="px-6 py-5 text-[9px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-[0.2em] text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-5 font-bold text-gray-400 dark:text-slate-600 text-xs">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-pc-blue dark:bg-slate-800 text-white flex items-center justify-center font-black text-[11px] shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-800 dark:text-white uppercase text-xs tracking-tight">{{ $user->name }}</span>
                                    <span class="text-[8px] font-black text-pc-orange uppercase tracking-widest">Activo en Sistema</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 font-bold text-gray-500 dark:text-gray-400 text-[10px] tracking-tight">{{ $user->email }}</td>
                        <td class="px-6 py-5">
                            @foreach($user->roles as $role)
                                <span class="px-3 py-1 text-[8px] font-black rounded-lg uppercase tracking-[0.2em] shadow-sm {{ $role->name === 'administrador' ? 'bg-pc-red/10 text-pc-red border border-pc-red/20' : 'bg-pc-blue/10 text-pc-blue border border-pc-blue/20 dark:bg-pc-blue/20' }}">
                                    <i class="fas fa-shield-alt mr-1"></i> {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-5 text-right space-x-1">
                            @can('view', $user)
                                <a href="{{ route('users.show', $user) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-pc-blue dark:text-gray-400 hover:bg-pc-blue hover:text-white transition-all shadow-sm" title="Bitácora">
                                    <i class="fas fa-clipboard-list text-xs"></i>
                                </a>
                            @endcan
                            @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-pc-orange hover:bg-pc-orange hover:text-white transition-all shadow-sm" title="Configurar">
                                    <i class="fas fa-cog text-xs"></i>
                                </a>
                            @endcan
                            {{-- Solo mostrar eliminar si no es el usuario actual --}}
                            @if(Auth::id() !== $user->id)
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline confirm-delete" data-label="{{ $user->name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-pc-red hover:bg-pc-red hover:text-white transition-all shadow-sm" 
                                                title="Revocar Acceso">
                                            <i class="fas fa-user-slash text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-8 px-2">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lógica de autocompletado y ordenamiento similar pero con estilos actualizados...
    const searchInput = document.getElementById('search_input');
    const suggestionsDiv = document.getElementById('suggestions');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const term = this.value.trim();
        if (term.length < 2) { suggestionsDiv.classList.add('hidden'); return; }
        debounceTimer = setTimeout(() => {
            fetch(`{{ route('users.autocomplete') }}?term=${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) { suggestionsDiv.classList.add('hidden'); return; }
                    suggestionsDiv.innerHTML = data.map(item => `
                        <div class="suggestion-item px-6 py-3 hover:bg-pc-blue/5 cursor-pointer border-b border-gray-50 transition-colors flex items-center gap-3" data-value="${item.value}">
                            <i class="fas fa-user text-gray-300"></i>
                            <span class="text-xs font-bold text-gray-700">${item.label}</span>
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

    const sortInput = document.getElementById('sortInput');
    document.querySelectorAll('th[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
            const field = header.getAttribute('data-sort');
            let current = sortInput.value;
            let newSort = field + (current.endsWith('_asc') ? '_desc' : '_asc');
            sortInput.value = newSort;
            document.getElementById('searchForm').submit();
        });
    });
</script>
@endpush