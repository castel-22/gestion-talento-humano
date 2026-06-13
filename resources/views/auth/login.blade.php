<x-guest-layout>
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="mb-4 text-center">
        <h2 class="text-xs font-black text-pc-orange uppercase tracking-[0.3em] mb-1">Acceso de Seguridad</h2>
        <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest opacity-60 transition-colors">Control de Integridad de Datos</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-stretch">
            <!-- Columna Izquierda: Credenciales -->
            <div class="md:col-span-7 flex flex-col justify-center space-y-4">
                <div class="group">
                    <label for="email" class="block text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-3 group-focus-within:text-pc-orange transition-colors">Identificador Institucional</label>
                    <div class="relative">
                        <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-id-badge text-gray-500 dark:text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                        </div>
                        <input id="email" 
                               class="input-auth-pc pl-12" 
                               type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="nombre.apellido@pcebolivar.gob.ve" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="group" x-data="{ show: false }">
                    <div class="flex justify-between items-center mb-3">
                        <label for="password" class="block text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] group-focus-within:text-pc-orange transition-colors">Clave de Encriptación</label>
                        <button type="button" @click="show = !show" class="text-[8px] font-black text-pc-orange hover:text-pc-blue dark:hover:text-white uppercase tracking-widest transition-all">
                            <span x-show="!show"><i class="fas fa-eye"></i> Ver</span>
                            <span x-show="show"><i class="fas fa-eye-slash"></i> Ocultar</span>
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-fingerprint text-gray-500 dark:text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                        </div>
                        <input id="password" 
                               class="input-auth-pc pl-12"
                               :type="show ? 'text' : 'password'"
                               name="password"
                               required autocomplete="current-password"
                               placeholder="••••••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Captcha --}}
                <div class="w-full mt-2 flex flex-col items-center sm:items-start justify-center">
                    <div class="scale-75 sm:scale-90 origin-left sm:origin-left p-1 bg-white dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/5 shadow-sm dark:shadow-none transition-all duration-500 overflow-hidden">
                        {!! NoCaptcha::display() !!}
                    </div>
                    @error('g-recaptcha-response')
                        <span class="text-[9px] font-black text-pc-red uppercase tracking-widest mt-1 pl-2">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Columna Derecha: Acciones y Envíos -->
            <div class="md:col-span-5 auth-right-col backdrop-blur-xl rounded-3xl p-6 flex flex-col justify-center gap-8 self-center transition-all duration-500 border bg-white/60 border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                <div class="flex flex-col gap-4">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <div class="relative">
                            <input id="remember_me" type="checkbox" class="sr-only peer" name="remember">
                            <div class="w-8 h-4 bg-gray-800 rounded-full border border-gray-700 transition-colors group-hover:border-pc-orange peer-checked:bg-pc-blue peer-checked:border-pc-blue dark:peer-checked:bg-pc-orange dark:peer-checked:border-pc-orange"></div>
                            <div class="dot absolute left-1 top-1 bg-gray-500 w-2 h-2 rounded-full transition-all peer-checked:translate-x-4 peer-checked:bg-white"></div>
                        </div>
                        <span class="ms-3 text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest group-hover:text-pc-blue dark:group-hover:text-white transition-colors">Mantener Sesión</span>
                    </label>

                    <a class="text-[9px] font-black text-gray-500 dark:text-gray-400 hover:text-pc-blue dark:hover:text-white uppercase tracking-widest transition-colors flex items-center gap-2 group" 
                       href="{{ route('password.questions.email') }}">
                        <div class="w-7 h-7 rounded-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 flex items-center justify-center group-hover:bg-pc-orange group-hover:border-pc-orange group-hover:text-white transition-all shadow-sm">
                            <i class="fas fa-unlock-keyhole text-[9px]"></i>
                        </div>
                        <span>Recuperar Acceso</span>
                    </a>
                </div>

                <button type="submit" class="btn-auth-pc flex items-center justify-center gap-3 group w-full py-2.5 rounded-2xl">
                    <span>Ingresar</span>
                    <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </div>
    </form>

    <div class="mt-4 text-center border-t border-gray-200 dark:border-white/5 pt-4 transition-colors duration-500">
        <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.3em] mb-4 transition-colors">Unidad de Gestión de Talento</p>
        <a href="{{ route('register') }}" 
           class="inline-block px-8 py-3 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-pc-blue dark:text-white hover:text-white hover:bg-pc-orange dark:hover:bg-pc-orange hover:border-pc-orange font-black text-[9px] uppercase tracking-[0.25em] rounded-xl transition-all duration-500 shadow-sm dark:shadow-2xl">
            Registrar Nuevo Operador
        </a>
    </div>

    {{-- Script de reCAPTCHA --}}
    {!! NoCaptcha::renderJs() !!}
</x-guest-layout>