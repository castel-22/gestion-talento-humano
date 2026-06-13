<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pc-orange/10 to-orange-50 dark:from-pc-orange/20 dark:to-orange-900/10 flex items-center justify-center mx-auto mb-4 shadow-sm">
            <i class="fas fa-shield-question text-2xl text-pc-orange"></i>
        </div>
        <h2 class="text-xs font-black text-pc-orange uppercase tracking-[0.3em] mb-1">Verificación de Identidad</h2>
        <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest opacity-60">Paso 1 de 3 — Ingresa tu correo institucional</p>
    </div>

    <form method="POST" action="{{ route('password.questions.verify-email') }}" class="space-y-6">
        @csrf

        <div class="group">
            <label for="email" class="block text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2 group-focus-within:text-pc-orange transition-colors">
                Correo Electrónico
            </label>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-300 dark:text-gray-600 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input id="email"
                       class="input-auth-pc pl-12"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       placeholder="usuario@pcebolivar.gob.ve" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-auth-pc flex items-center justify-center gap-3 group">
                <span>Continuar</span>
                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform opacity-70"></i>
            </button>
        </div>
    </form>

    <div class="mt-6 text-center border-t border-gray-100 dark:border-white/5 pt-6">
        <a href="{{ route('password.choice') }}"
           class="text-[9px] font-black text-gray-400 dark:text-gray-500 hover:text-pc-orange uppercase tracking-widest transition-colors flex items-center justify-center gap-2 group">
            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            Elegir otro método
        </a>
    </div>
</x-guest-layout>
