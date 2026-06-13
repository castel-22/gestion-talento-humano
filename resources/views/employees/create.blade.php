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
                <a href="{{ route('employees.index') }}" class="text-sm text-gray-700 hover:text-pc-orange">Personal</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">{{ isset($employee) ? 'Editar' : 'Nuevo' }} Integrante</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ tab: 'personal' }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-pc-blue uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-user-edit text-pc-orange"></i> {{ isset($employee) ? 'Actualizar' : 'Registrar' }} Personal
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Complete todos los campos obligatorios marcados con (*)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 font-black text-[10px] uppercase px-6 py-3 rounded-xl transition-all">
                Cancelar
            </a>
        </div>
    </div>

    {{-- Navegación de Pestañas (Minimiza Scroll) --}}
    <div class="flex flex-wrap gap-2 mb-6 bg-gray-50 p-1.5 rounded-2xl border border-gray-100">
        <button @click="tab = 'personal'" :class="tab === 'personal' ? 'bg-pc-blue text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-white'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-user text-xs"></i> <span class="hidden sm:inline">Datos Personales</span>
        </button>
        <button @click="tab = 'academic'" :class="tab === 'academic' ? 'bg-pc-blue text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-white'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-graduation-cap text-xs"></i> <span class="hidden sm:inline">Académicos</span>
        </button>
        <button @click="tab = 'laboral'" :class="tab === 'laboral' ? 'bg-pc-blue text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-white'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-briefcase text-xs"></i> <span class="hidden sm:inline">Laborales</span>
        </button>
        <button @click="tab = 'documents'" :class="tab === 'documents' ? 'bg-pc-blue text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-white'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-file-alt text-xs"></i> <span class="hidden sm:inline">Documentos</span>
        </button>
    </div>

    <form method="POST" action="{{ isset($employee) ? route('employees.update', $employee) : route('employees.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @if(isset($employee)) @method('PUT') @endif

        {{-- Sección Personal --}}
        <div x-show="tab === 'personal'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="card-pc p-8">
            <h3 class="text-lg font-black text-pc-blue uppercase mb-8 pb-4 border-b border-gray-50 flex items-center gap-3">
                <i class="fas fa-id-card text-pc-orange"></i> Información de Identidad
            </h3>
            @include('employees._form_personal')
            <div class="mt-10 flex justify-end">
                <button type="button" @click="tab = 'academic'" class="bg-pc-orange text-white font-black text-[10px] uppercase px-8 py-4 rounded-xl shadow-lg shadow-orange-100 hover:bg-orange-600 transition-all flex items-center gap-2">
                    Siguiente: Académicos <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- Sección Académica --}}
        <div x-show="tab === 'academic'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="card-pc p-8">
            <h3 class="text-lg font-black text-pc-blue uppercase mb-8 pb-4 border-b border-gray-50 flex items-center gap-3">
                <i class="fas fa-graduation-cap text-pc-orange"></i> Formación y Estudios
            </h3>
            @include('employees._form_academic')
            <div class="mt-10 flex justify-between">
                <button type="button" @click="tab = 'personal'" class="text-gray-400 font-black text-[10px] uppercase px-6 py-4 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Anterior
                </button>
                <button type="button" @click="tab = 'laboral'" class="bg-pc-orange text-white font-black text-[10px] uppercase px-8 py-4 rounded-xl shadow-lg shadow-orange-100 hover:bg-orange-600 transition-all flex items-center gap-2">
                    Siguiente: Laboral <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- Sección Laboral --}}
        <div x-show="tab === 'laboral'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="card-pc p-8">
            <h3 class="text-lg font-black text-pc-blue uppercase mb-8 pb-4 border-b border-gray-50 flex items-center gap-3">
                <i class="fas fa-briefcase text-pc-orange"></i> Datos Institucionales
            </h3>
            @include('employees._form_laboral')
            <div class="mt-10 flex justify-between">
                <button type="button" @click="tab = 'academic'" class="text-gray-400 font-black text-[10px] uppercase px-6 py-4 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Anterior
                </button>
                <button type="button" @click="tab = 'documents'" class="bg-pc-orange text-white font-black text-[10px] uppercase px-8 py-4 rounded-xl shadow-lg shadow-orange-100 hover:bg-orange-600 transition-all flex items-center gap-2">
                    Siguiente: Documentos <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- Sección Documentos --}}
        <div x-show="tab === 'documents'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="card-pc p-8">
            <h3 class="text-lg font-black text-pc-blue uppercase mb-8 pb-4 border-b border-gray-50 flex items-center gap-3">
                <i class="fas fa-file-upload text-pc-orange"></i> Archivos y Soportes
            </h3>
            @include('employees._form_documents')
            <div class="mt-10 flex justify-between pt-8 border-t border-gray-50">
                <button type="button" @click="tab = 'laboral'" class="text-gray-400 font-black text-[10px] uppercase px-6 py-4 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Anterior
                </button>
                <button type="submit" class="bg-pc-blue text-white font-black text-[10px] uppercase px-12 py-4 rounded-xl shadow-xl shadow-blue-100 hover:bg-blue-800 transition-all flex items-center gap-3">
                    <i class="fas fa-save text-xs"></i> Finalizar y Guardar
                </button>
            </div>
        </div>
    </form>
</div>
@endsection