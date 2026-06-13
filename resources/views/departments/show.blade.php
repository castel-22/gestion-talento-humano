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
                <a href="{{ route('departments.index') }}" class="text-sm text-gray-700 hover:text-pc-orange">Departamentos</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Detalles de Unidad</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Cabecera de la Unidad --}}
    <div class="card-pc p-10 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-5 pointer-events-none translate-x-1/4 -translate-y-1/4">
            <i class="fas fa-building text-[240px]"></i>
        </div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-10 relative z-10">
            <div class="w-40 h-40 rounded-3xl bg-white border-2 border-gray-100 shadow-2xl flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($department->logo)
                    <img src="{{ Storage::url($department->logo) }}" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-building text-pc-blue text-5xl"></i>
                @endif
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="mb-4">
                    <span class="text-[10px] font-black text-pc-orange uppercase tracking-widest bg-pc-orange/10 px-3 py-1 rounded-full border border-pc-orange/20">Unidad Operativa #{{ $department->id }}</span>
                    <h2 class="text-4xl font-black text-pc-blue uppercase tracking-tight mt-3">{{ $department->name }}</h2>
                </div>
                <p class="text-gray-500 font-medium text-lg leading-relaxed max-w-2xl">
                    {{ $department->description ?: 'Esta unidad operativa centraliza las gestiones estratégicas asignadas según el organigrama institucional.' }}
                </p>
                <div class="mt-8 flex flex-wrap justify-center md:justify-start gap-6">
                    <div class="flex flex-col">
                        <span class="text-3xl font-black text-pc-blue">{{ $department->employees_count }}</span>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Integrantes Asignados</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 w-full md:w-auto">
                @can('update', $department)
                    <a href="{{ route('departments.edit', $department) }}" class="bg-pc-orange hover:bg-orange-600 text-white font-black text-[10px] uppercase px-8 py-4 rounded-xl shadow-lg shadow-orange-100 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-edit"></i> Editar Unidad
                    </a>
                @endcan
                <a href="{{ route('departments.index') }}" class="text-gray-400 hover:text-pc-blue font-black text-[10px] uppercase px-8 py-3 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>

    {{-- Listado de Integrantes --}}
    <div class="card-pc p-8">
        <h3 class="text-sm font-black text-pc-blue uppercase mb-8 flex items-center gap-3">
            <i class="fas fa-users text-pc-orange"></i> Personal Adscrito a la Unidad
        </h3>

        @if($department->employees->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($department->employees as $employee)
                    <a href="{{ route('employees.show', $employee) }}" class="group bg-gray-50/50 hover:bg-white p-4 rounded-2xl border border-gray-100 hover:border-pc-blue hover:shadow-xl transition-all flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-pc-blue text-white flex items-center justify-center font-black text-xs shadow-lg group-hover:scale-110 transition-transform">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-black text-gray-800 uppercase truncate group-hover:text-pc-blue transition-colors">{{ $employee->full_name }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1 truncate">{{ $employee->position ?: 'Sin Cargo' }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-200 group-hover:text-pc-orange group-hover:translate-x-1 transition-all"></i>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 bg-gray-50/50 rounded-3xl border border-dashed border-gray-200">
                <i class="fas fa-user-slash text-gray-200 text-5xl mb-4"></i>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No existen integrantes asignados a esta unidad actualmente</p>
            </div>
        @endif
    </div>
</div>
@endsection