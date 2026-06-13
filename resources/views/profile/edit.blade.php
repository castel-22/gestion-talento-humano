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
                <span class="text-[10px] font-black text-pc-orange uppercase tracking-widest">Mi Perfil Operativo</span>
            </div>
        </li>
    </ol>
</nav>
@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const placeholder = document.getElementById('avatar-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Tarjeta de Identidad --}}
        <div class="lg:col-span-1">
            <div class="card-pc p-8 text-center sticky top-24 dark:bg-slate-900 dark:border-slate-800">
                <div class="relative group mx-auto w-32 h-32 mb-6">
                    <div class="w-full h-full rounded-3xl bg-pc-blue text-white flex items-center justify-center text-4xl font-black shadow-2xl shadow-blue-900/20 dark:shadow-none overflow-hidden border-4 border-white dark:border-slate-800">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover" id="avatar-preview">
                        @else
                            <span id="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            <img src="#" class="w-full h-full object-cover hidden" id="avatar-preview">
                        @endif
                    </div>
                    {{-- Overlay de subida --}}
                    <label for="avatar-input" class="absolute inset-0 bg-pc-blue/60 backdrop-blur-sm rounded-3xl opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center text-white gap-2 border-2 border-dashed border-pc-orange/50 m-1">
                        <i class="fas fa-camera-retro text-2xl"></i>
                        <span class="text-[9px] font-black uppercase tracking-[0.2em]">Actualizar</span>
                    </label>
                </div>

                <h2 class="text-xl font-black text-pc-blue dark:text-white uppercase tracking-tight">{{ $user->name }}</h2>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    @foreach($user->roles as $role)
                        <span class="bg-pc-orange/10 text-pc-orange text-[9px] font-black uppercase tracking-[0.2em] px-4 py-1.5 rounded-full border border-pc-orange/20 shadow-sm shadow-orange-500/5">
                            <i class="fas fa-shield-alt mr-1"></i> {{ $role->name }}
                        </span>
                    @endforeach
                </div>
                <p class="text-gray-400 dark:text-gray-500 text-[10px] font-bold mt-6 uppercase tracking-[0.2em] opacity-60">Operativo desde {{ $user->created_at->format('M Y') }}</p>
                
                <div class="mt-8 pt-8 border-t border-gray-100 dark:border-slate-800 grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-black text-pc-blue dark:text-pc-orange">{{ $user->securityAnswers->count() }}</p>
                        <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Protocolos</p>
                    </div>
                    <div class="text-center border-l border-gray-100 dark:border-slate-800">
                        <p class="text-2xl font-black text-green-500">ACT</p>
                        <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Estado</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formularios de Edición --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Información de Cuenta --}}
            <div class="card-pc p-8 dark:bg-slate-900 dark:border-slate-800">
                <h3 class="text-base font-black text-pc-blue dark:text-white uppercase mb-8 flex items-center gap-3">
                    <i class="fas fa-id-card text-pc-orange"></i> Identidad Institucional
                </h3>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    {{-- Input de archivo oculto (referenciado por el label en la tarjeta) --}}
                    <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*" onchange="previewAvatar(this)">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="group">
                            <label class="label-pc dark:text-gray-400 mb-2">Nombre de Agente</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-4 top-3.5 text-gray-300 dark:text-slate-600"></i>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-pc pl-11 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                            </div>
                            @error('name') <p class="text-pc-red text-[9px] font-black mt-2 uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div class="group">
                            <label class="label-pc dark:text-gray-400 mb-2">Canal de Comunicación</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-300 dark:text-slate-600"></i>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-pc pl-11 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                            </div>
                            @error('email') <p class="text-pc-red text-[9px] font-black mt-2 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="btn-pc-secondary px-10 py-3 shadow-xl shadow-blue-900/20 active:scale-95">
                            <i class="fas fa-save mr-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            {{-- Seguridad y Contraseña --}}
            <div class="card-pc p-8 dark:bg-slate-900 dark:border-slate-800">
                <h3 class="text-base font-black text-pc-blue dark:text-white uppercase mb-8 flex items-center gap-3">
                    <i class="fas fa-shield-alt text-pc-orange"></i> Protección de Credenciales
                </h3>
                <form method="POST" action="{{ route('password.update') }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="group">
                            <label class="label-pc dark:text-gray-400 mb-2">Clave Vigente</label>
                            <input type="password" name="current_password" class="input-pc dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                        </div>
                        <div class="group">
                            <label class="label-pc dark:text-gray-400 mb-2">Clave Nueva</label>
                            <input type="password" name="password" class="input-pc dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                        </div>
                        <div class="group">
                            <label class="label-pc dark:text-gray-400 mb-2">Confirmación</label>
                            <input type="password" name="password_confirmation" class="input-pc dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                        </div>
                    </div>
                    
                    @if ($errors->updatePassword->any())
                        <div class="bg-red-50 dark:bg-red-900/10 border-l-4 border-pc-red p-4 rounded-r-xl">
                            <ul class="text-[10px] text-pc-red font-black uppercase tracking-widest space-y-1">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <li><i class="fas fa-exclamation-triangle mr-1"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="btn-pc-primary px-10 py-3 shadow-xl shadow-orange-900/20 active:scale-95">
                            <i class="fas fa-key mr-1"></i> Actualizar Credenciales
                        </button>
                    </div>
                </form>
            </div>

            {{-- Preguntas de Seguridad --}}
            @if($user->securityAnswers->count() >= 4)
                {{-- Solo Lectura (Ya Configurado) --}}
                <div class="card-pc p-8 bg-gray-50/50 border-dashed">
                    <h3 class="text-sm font-black text-gray-500 uppercase mb-6 flex items-center gap-3">
                        <i class="fas fa-shield-alt text-gray-400"></i> Recuperación de Cuenta
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->securityAnswers as $answer)
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-[9px] font-black text-pc-orange uppercase tracking-widest mb-1">{{ $answer->question->question }}</p>
                                <p class="text-xs font-bold text-gray-800">●●●●●●●●●●</p>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-6 text-[10px] text-gray-400 font-bold leading-relaxed uppercase">
                        <i class="fas fa-info-circle mr-1"></i> Por seguridad institucional, las preguntas de recuperación solo pueden ser modificadas por un administrador de sistemas.
                    </p>
                </div>
            @else
                {{-- Formulario para Configurar --}}
                <div class="card-pc p-8 dark:bg-slate-900 dark:border-slate-800 border-2 border-pc-orange">
                    <h3 class="text-base font-black text-pc-blue dark:text-white uppercase mb-4 flex items-center gap-3">
                        <i class="fas fa-lock text-pc-orange"></i> Configuración de Seguridad Requerida
                    </h3>
                    <p class="text-xs text-gray-500 mb-6 font-medium">Debes configurar tus 4 preguntas de seguridad para garantizar la recuperación de tu cuenta.</p>
                    
                    <form method="POST" action="{{ route('profile.security-questions.store') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @for ($i = 0; $i < 4; $i++)
                                <div class="bg-gray-50 dark:bg-slate-800 p-4 rounded-xl border border-gray-200 dark:border-slate-700">
                                    <div class="mb-3">
                                        <label class="label-pc dark:text-gray-400 mb-2">Pregunta {{ $i + 1 }}</label>
                                        <select name="security_questions[{{ $i }}][question_id]" class="input-pc dark:bg-slate-900 dark:border-slate-600 dark:text-white" required>
                                            <option value="" disabled selected>Selecciona una pregunta...</option>
                                            @foreach($securityQuestions as $question)
                                                <option value="{{ $question->id }}">{{ $question->question }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label-pc dark:text-gray-400 mb-2">Respuesta Secreta</label>
                                        <input type="text" name="security_questions[{{ $i }}][answer]" class="input-pc dark:bg-slate-900 dark:border-slate-600 dark:text-white" required minlength="2">
                                    </div>
                                </div>
                            @endfor
                        </div>

                        @if(session('error'))
                            <div class="bg-red-50 dark:bg-red-900/10 border-l-4 border-pc-red p-4 rounded-r-xl">
                                <p class="text-[10px] text-pc-red font-black uppercase tracking-widest"><i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}</p>
                            </div>
                        @endif

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-pc-primary px-10 py-3 shadow-xl shadow-orange-900/20 active:scale-95">
                                <i class="fas fa-save mr-1"></i> Guardar Preguntas de Seguridad
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection