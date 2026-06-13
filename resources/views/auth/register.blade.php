<x-guest-layout>
    <div x-data="{ step: 1 }" class="w-full">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xs font-black text-pc-orange uppercase tracking-[0.3em] mb-1">Registro de Operador</h2>
                <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest opacity-60 transition-colors" x-text="step === 1 ? 'Paso 1: Credenciales de Identidad' : 'Paso 2: Protocolo de Seguridad'"></p>
            </div>
            <div class="flex gap-2">
                <div class="w-10 h-1.5 rounded-full transition-all duration-500 shadow-sm" :class="step >= 1 ? 'bg-pc-orange shadow-orange-500/20' : 'bg-gray-200 dark:bg-gray-800'"></div>
                <div class="w-10 h-1.5 rounded-full transition-all duration-500 shadow-sm" :class="step >= 2 ? 'bg-pc-orange shadow-orange-500/20' : 'bg-gray-200 dark:bg-gray-800'"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" @submit="if(step < 2) { $event.preventDefault(); step = 2; }">
            @csrf

            {{-- Paso 1: Datos de Acceso --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-12" class="space-y-4">
                <div class="group">
                    <label for="name" class="block text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-3 group-focus-within:text-pc-orange transition-colors">Nombre de Agente</label>
                    <div class="relative">
                        <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user-shield text-gray-500 dark:text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                        </div>
                        <input id="name" class="input-auth-pc pl-12" type="text" name="name" :value="old('name')" required autofocus placeholder="Ej: Insp. Juan Pérez" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="group">
                    <label for="email" class="block text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-3 group-focus-within:text-pc-orange transition-colors">Correo Operativo</label>
                    <div class="relative">
                        <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope-open-text text-gray-500 dark:text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                        </div>
                        <input id="email" class="input-auth-pc pl-12" type="email" name="email" :value="old('email')" required placeholder="agente@pcebolivar.gob.ve" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="group">
                        <label for="password" class="block text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-3 group-focus-within:text-pc-orange transition-colors">Clave de Acceso</label>
                        <div class="relative">
                            <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-500 dark:text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                            </div>
                            <input id="password" class="input-auth-pc pl-12" type="password" name="password" required placeholder="••••••••" />
                        </div>
                    </div>

                    <div class="group">
                        <label for="password_confirmation" class="block text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-3 group-focus-within:text-pc-orange transition-colors">Verificar Clave</label>
                        <div class="relative">
                            <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-check-double text-gray-500 dark:text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                            </div>
                            <input id="password_confirmation" class="input-auth-pc pl-12" type="password" name="password_confirmation" required placeholder="••••••••" />
                        </div>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />

                <button type="button" @click="step = 2" class="btn-auth-pc mt-6 group">
                    <span>Configurar Seguridad</span>
                    <i class="fas fa-shield-halved ml-2 group-hover:rotate-12 transition-transform"></i>
                </button>
            </div>

            {{-- Paso 2: Seguridad y Verificación --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-12" class="space-y-4">
                <div class="p-3 bg-white dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm dark:shadow-inner transition-colors duration-500">
                    <h3 class="text-[9px] font-black text-pc-orange uppercase tracking-[0.2em] mb-2 flex items-center gap-2">
                        <i class="fas fa-fingerprint"></i> Autenticación de Respaldo
                    </h3>
                    <p class="text-[8px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest italic leading-relaxed transition-colors">Defina 4 respuestas clave para la recuperación táctica de su cuenta.</p>
                </div>

                <div class="space-y-3 max-h-[220px] overflow-y-auto pr-2 custom-scrollbar">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="space-y-2 p-3 bg-gray-50 dark:bg-black/20 rounded-2xl border border-gray-100 dark:border-white/5 group hover:border-pc-orange/30 transition-all shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] dark:shadow-none">
                            <label class="block text-[8px] font-black text-pc-orange/80 dark:text-pc-orange/50 uppercase tracking-[0.3em] transition-colors">Protocolo {{ $i + 1 }}</label>
                            <select name="security_questions[{{ $i }}][question_id]" class="input-auth-pc py-2 text-[10px] border-none shadow-sm dark:shadow-xl" required>
                                <option value="">Seleccione Pregunta de Seguridad...</option>
                                @foreach (\App\Models\SecurityQuestion::all() as $question)
                                    <option value="{{ $question->id }}">{{ $question->question }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="security_questions[{{ $i }}][answer]" placeholder="Respuesta de Validación..." class="input-auth-pc py-2 text-[10px] border-none shadow-sm dark:shadow-xl" required>
                        </div>
                    @endfor
                </div>

                {{-- Captcha --}}
                <div class="mt-4 flex justify-center scale-75 sm:scale-90 p-2 bg-white dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm dark:shadow-none transition-colors duration-500">
                    {!! NoCaptcha::display() !!}
                </div>
                @error('g-recaptcha-response')
                    <p class="text-[9px] font-black text-pc-red uppercase text-center mt-2">{{ $message }}</p>
                @enderror

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button type="button" @click="step = 1" class="py-3 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 font-black text-[9px] uppercase tracking-widest rounded-2xl hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                        <i class="fas fa-chevron-left mr-2"></i> Volver
                    </button>
                    <button type="submit" class="btn-auth-pc">
                        Activar Cuenta
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="text-center mt-4 border-t border-gray-200 dark:border-white/5 pt-4 transition-colors duration-500">
        <a class="text-[9px] font-black text-gray-500 dark:text-gray-400 hover:text-pc-orange uppercase tracking-[0.25em] transition-colors" href="{{ route('login') }}">
            ¿Ya posee acceso? <span class="text-pc-orange">Iniciar Sesión</span>
        </a>
    </div>

    {{-- Estilos personalizados para el scrollbar interno --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    </style>

    {{-- Script de reCAPTCHA --}}
    {!! NoCaptcha::renderJs() !!}
</x-guest-layout>