@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-[10px] font-black text-gray-400 hover:text-pc-orange uppercase tracking-widest inline-flex items-center transition-colors">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-700 text-[8px] mx-2"></i>
                <a href="{{ route('users.index') }}" class="text-[10px] font-black text-gray-400 hover:text-pc-orange uppercase tracking-widest transition-colors">Control de Operadores</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-700 text-[8px] mx-2"></i>
                <span class="text-[10px] font-black text-pc-orange uppercase tracking-widest">Modificar Credenciales</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="card-pc p-8 dark:bg-slate-900 dark:border-slate-800">
        <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-100 dark:border-slate-800">
            <div class="w-12 h-12 rounded-2xl bg-pc-blue/10 text-pc-blue flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-pc-blue dark:text-white uppercase tracking-tight">Editar Usuario: {{ $user->name }}</h2>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-[0.2em] opacity-60">Formulario de edición de usuario en el sistema</p>
            </div>
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="group">
                    <label class="label-pc dark:text-gray-400 mb-3">Nombre Completo</label>
                    <div class="relative">
                        <i class="fas fa-user-tag absolute left-4 top-3.5 text-gray-400 dark:text-slate-600 transition-colors group-focus-within:text-pc-orange"></i>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                    </div>
                    @error('name') <p class="text-pc-red text-[9px] font-black mt-2 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="group">
                    <label class="label-pc dark:text-gray-400 mb-3">Correo Electrónico</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-400 dark:text-slate-600 transition-colors group-focus-within:text-pc-orange"></i>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                    </div>
                    @error('email') <p class="text-pc-red text-[9px] font-black mt-2 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="group">
                    <label class="label-pc dark:text-gray-400 mb-3">Nueva Contraseña <span class="text-[8px] opacity-50">(Opcional)</span></label>
                    <div class="relative">
                        <i class="fas fa-key absolute left-4 top-3.5 text-gray-400 dark:text-slate-600 transition-colors group-focus-within:text-pc-orange"></i>
                        <input type="password" name="password" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="Dejar vacío si no cambia">
                    </div>
                </div>

                <div class="group">
                    <label class="label-pc dark:text-gray-400 mb-3">Confirmar Contraseña</label>
                    <div class="relative">
                        <i class="fas fa-check-double absolute left-4 top-3.5 text-gray-400 dark:text-slate-600 transition-colors group-focus-within:text-pc-orange"></i>
                        <input type="password" name="password_confirmation" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="md:col-span-2 group">
                    <label class="label-pc dark:text-gray-400 mb-3">Rol de Usuario</label>
                    <div class="relative">
                        <i class="fas fa-shield-alt absolute left-4 top-3.5 text-gray-400 dark:text-slate-600 transition-colors group-focus-within:text-pc-orange"></i>
                        <select name="role" class="input-pc pl-12 dark:bg-slate-800 dark:border-slate-700 dark:text-white appearance-none" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ strtoupper($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 dark:border-slate-800">
                <div class="flex items-center gap-3 mb-8">
                    <i class="fas fa-shield-halved text-pc-orange"></i>
                    <h3 class="text-[11px] font-black text-pc-blue dark:text-white uppercase tracking-[0.2em]">Preguntas de Seguridad</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 opacity-80 hover:opacity-100 transition-opacity">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="p-6 bg-gray-50/50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-800">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-2">Pregunta {{ $i }}</label>
                                    <select name="security_questions[{{ $i }}][question_id]" class="input-pc text-[10px] py-2 border-none dark:bg-slate-900">
                                        <option value="">-- MANTENER PREGUNTA ACTUAL --</option>
                                        @foreach($securityQuestions as $question)
                                            <option value="{{ $question->id }}">{{ $question->question }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <input type="text" name="security_questions[{{ $i }}][answer]" class="input-pc text-[10px] py-2 border-none dark:bg-slate-900" placeholder="Nueva Respuesta (Opcional)">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-10 border-t border-gray-100 dark:border-slate-800">
                <a href="{{ route('users.index') }}" class="px-8 py-3 text-[10px] font-black text-gray-500 hover:text-pc-red uppercase tracking-widest transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="btn-pc-primary px-10 py-3 shadow-xl shadow-orange-900/20 active:scale-95">
                    <i class="fas fa-save mr-2"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection