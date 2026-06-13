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
                <span class="text-sm text-pc-orange font-medium">Actualizar Expediente</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ 
    tab: window.location.hash ? window.location.hash.substring(1) : 'personal',
    init() {
        window.addEventListener('hashchange', () => {
            this.tab = window.location.hash.substring(1);
        });
    }
}">
    {{-- Cabecera de Edición --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h2 class="text-2xl font-black text-pc-blue uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-user-edit text-pc-orange"></i> Editar: {{ $employee->full_name }}
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Modificación de información técnica y administrativa</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.show', $employee) }}" class="bg-white border border-gray-200 text-gray-500 font-black text-[10px] uppercase px-6 py-3 rounded-xl hover:bg-gray-50 transition-all flex items-center gap-2">
                <i class="fas fa-eye"></i> Ver Expediente
            </a>
        </div>
    </div>

    {{-- Navegación por Pestañas (Selección de sección a actualizar) --}}
    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 overflow-hidden border border-gray-100 mb-8">
        <div class="flex border-b border-gray-50 p-2 bg-gray-50/50">
            <button @click="tab = 'personal'" :class="tab === 'personal' ? 'bg-white text-pc-blue shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 px-6 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                <i class="fas fa-user"></i> Personal
            </button>
            <button @click="tab = 'academic'" :class="tab === 'academic' ? 'bg-white text-pc-blue shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 px-6 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                <i class="fas fa-graduation-cap"></i> Académico
            </button>
            <button @click="tab = 'laboral'" :class="tab === 'laboral' ? 'bg-white text-pc-blue shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 px-6 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                <i class="fas fa-briefcase"></i> Laboral
            </button>
            <button @click="tab = 'documents'" :class="tab === 'documents' ? 'bg-white text-pc-blue shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 px-6 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                <i class="fas fa-file-invoice"></i> Documentos
            </button>
        </div>

        <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-8">
                {{-- Sección Personal --}}
                <div x-show="tab === 'personal'" x-transition:enter="transition ease-out duration-200">
                    <div class="mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-pc-blue uppercase tracking-widest">Identidad y Contacto</h3>
                    </div>
                    @include('employees._form_personal', ['employee' => $employee])
                </div>

                {{-- Sección Académica --}}
                <div x-show="tab === 'academic'" x-transition:enter="transition ease-out duration-200">
                    <div class="mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-pc-blue uppercase tracking-widest">Formación Profesional</h3>
                    </div>
                    @include('employees._form_academic', ['employee' => $employee])
                </div>

                {{-- Sección Laboral --}}
                <div x-show="tab === 'laboral'" x-transition:enter="transition ease-out duration-200">
                    <div class="mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-pc-blue uppercase tracking-widest">Jerarquía y Ubicación Administrativa</h3>
                    </div>
                    @include('employees._form_laboral', ['employee' => $employee])
                </div>

                {{-- Sección Documentos --}}
                <div x-show="tab === 'documents'" x-transition:enter="transition ease-out duration-200">
                    <div class="mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-pc-blue uppercase tracking-widest">Soportes Digitales</h3>
                    </div>
                    @include('employees._form_documents', ['employee' => $employee])
                </div>

                {{-- Acciones Finales --}}
                <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">
                        * Los cambios afectarán el expediente histórico del integrante.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('employees.index') }}" class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-pc-red transition-colors">Cancelar</a>
                        <button type="submit" class="bg-pc-blue text-white font-black text-[10px] uppercase px-10 py-4 rounded-xl shadow-xl shadow-blue-100 hover:bg-blue-800 transition-all flex items-center gap-3">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection