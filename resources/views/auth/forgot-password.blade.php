<x-guest-layout>
    <div class="mb-8">
        <h2 class="outfit-font text-xl text-pc-blue uppercase tracking-tight mb-2 text-center">Recuperación de Acceso</h2>
        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-relaxed text-center">
            Indíquenos su correo institucional para enviarle las instrucciones de restablecimiento de seguridad.
        </p>
    </div>

    <!-- Estado de sesión (mensajes de éxito) -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Correo Electrónico -->
        <div class="group">
            <label for="email" class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-pc-orange transition-colors">Correo Electrónico</label>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-300 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input id="email" 
                       class="input-auth-pc pl-12" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       required 
                       autofocus 
                       placeholder="usuario@pcebolivar.gob.ve" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-auth-pc flex items-center justify-center gap-3">
                <span>Enviar Instrucciones</span>
                <i class="fas fa-paper-plane text-[10px] opacity-50"></i>
            </button>
        </div>
    </form>

    <div class="mt-8 text-center">
        <a href="{{ route('login') }}" class="text-[10px] font-black text-pc-blue hover:text-pc-orange uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
            <i class="fas fa-arrow-left"></i> Volver al Inicio de Sesión
        </a>
    </div>
</x-guest-layout>
