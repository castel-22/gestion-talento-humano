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
                <span class="text-sm text-pc-orange font-medium">Nueva Unidad</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card-pc p-8">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-50">
            <div class="w-12 h-12 rounded-xl bg-pc-orange/10 text-pc-orange flex items-center justify-center text-xl">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-pc-blue uppercase tracking-tight">Registrar Nueva Unidad</h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Configure los detalles operativos de la unidad</p>
            </div>
        </div>

        <form method="POST" action="{{ route('departments.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="label-pc">Nombre Institucional de la Unidad *</label>
                <div class="relative">
                    <i class="fas fa-id-card-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           placeholder="Ej: Dirección de Operaciones y Rescate"
                           class="input-pc pl-12" required>
                </div>
                @error('name') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="label-pc">Descripción de Funciones y Alcance</label>
                <textarea name="description" id="description" rows="4" 
                          placeholder="Describa brevemente la misión y responsabilidades de esta unidad..."
                          class="input-pc min-h-[120px]">{{ old('description') }}</textarea>
                @error('description') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label-pc">Imagen / Emblema de la Unidad (Opcional)</label>
                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-2xl group hover:border-pc-orange transition-all bg-gray-50/50">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-image text-gray-300 text-4xl mb-4 group-hover:text-pc-orange transition-colors"></i>
                        <div class="flex text-xs text-gray-600">
                            <label for="logo" class="relative cursor-pointer bg-white rounded-md font-black text-pc-blue hover:text-pc-orange transition-all px-2 py-1 shadow-sm">
                                <span>Subir archivo</span>
                                <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-2 font-bold uppercase text-[9px] flex items-center">o arrastre el archivo aquí</p>
                        </div>
                        <p class="text-[8px] text-gray-400 font-bold uppercase">PNG, JPG hasta 2MB</p>
                    </div>
                </div>
                @error('logo') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end items-center gap-4 pt-6">
                <a href="{{ route('departments.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-pc-blue transition-colors">
                    Descartar
                </a>
                <button type="submit" class="bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-10 py-4 rounded-xl shadow-xl shadow-blue-100 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Unidad
                </button>
            </div>
        </form>
    </div>
</div>
@endsection