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
                <span class="text-sm text-pc-orange font-medium">Ciclos de Guardia</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Cabecera --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-pc-blue uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-shield-halved text-pc-orange"></i> Rotaciones Estratégicas
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Configuración de ciclos operativos y turnos de guardia</p>
        </div>
        @can('create', App\Models\GuardRotation::class)
            <a href="{{ route('guard-rotations.create') }}" class="bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-8 py-4 rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Nueva Rotación
            </a>
        @endcan
    </div>

    {{-- Grid de Rotaciones --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($rotations as $rotation)
            <div class="card-pc group hover:-translate-y-2 transition-all duration-300 relative overflow-hidden flex flex-col h-full border-t-4 {{ $rotation->is_active ? 'border-t-green-500' : 'border-t-gray-300' }}">
                <div class="p-8 flex-1">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-pc-blue group-hover:bg-pc-orange group-hover:text-white transition-all shadow-inner">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                        <span class="px-3 py-1 text-[8px] font-black rounded-lg uppercase tracking-widest {{ $rotation->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $rotation->is_active ? 'Ciclo Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <h3 class="text-sm font-black text-pc-blue uppercase tracking-tight mb-3 group-hover:text-pc-orange transition-colors">{{ $rotation->name }}</h3>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed line-clamp-3">
                        {{ $rotation->description ?: 'No se ha definido una descripción detallada para este ciclo de rotación operativa.' }}
                    </p>
                </div>

                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center mt-auto">
                    <div class="flex gap-2">
                        <a href="{{ route('guard-rotations.calendar', $rotation) }}" class="flex items-center gap-2 text-[9px] font-black text-pc-blue uppercase tracking-widest hover:text-pc-orange transition-colors">
                            <i class="fas fa-eye text-xs"></i> Ver Cuadrante
                        </a>
                    </div>
                    <div class="flex gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                        @can('update', $rotation)
                            <a href="{{ route('guard-rotations.edit', $rotation) }}" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-indigo-500 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                        @endcan
                        @can('delete', $rotation)
                            <form action="{{ route('guard-rotations.destroy', $rotation) }}" method="POST" class="inline confirm-delete" data-label="{{ $rotation->name }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-pc-red flex items-center justify-center hover:bg-pc-red hover:text-white transition-all">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-24 px-4 bg-white/50 dark:bg-slate-900/40 rounded-3xl border-2 border-dashed border-gray-200 dark:border-slate-800 backdrop-blur-sm transition-colors">
                <div class="relative mb-6 group">
                    <!-- Efecto de brillo detrás -->
                    <div class="absolute inset-0 bg-pc-orange/20 dark:bg-pc-orange/10 blur-xl rounded-full transform group-hover:scale-125 transition-transform duration-500"></div>
                    <!-- Contenedor del ícono -->
                    <div class="relative w-24 h-24 bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl dark:shadow-slate-900/50 flex items-center justify-center border border-gray-100 dark:border-slate-700 transform group-hover:-translate-y-2 transition-all duration-500">
                        <i class="fas fa-shield-halved text-4xl text-gray-300 dark:text-slate-600 group-hover:text-pc-orange transition-colors duration-500"></i>
                    </div>
                </div>
                
                <h3 class="text-lg font-black text-pc-blue dark:text-gray-200 uppercase tracking-widest mb-2 text-center">Sin Ciclos Operativos</h3>
                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center max-w-md mb-8 leading-relaxed">
                    No se han configurado rotaciones estratégicas ni turnos de guardia. Comience creando un nuevo ciclo para organizar al personal.
                </p>

                @can('create', App\Models\GuardRotation::class)
                    <a href="{{ route('guard-rotations.create') }}" class="bg-pc-orange hover:bg-orange-600 text-white font-black text-[10px] uppercase px-8 py-4 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transform hover:-translate-y-1 transition-all flex items-center gap-3">
                        <i class="fas fa-plus-circle text-sm"></i> Configurar Primera Rotación
                    </a>
                @endcan
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $rotations->links() }}
    </div>
</div>
@endsection