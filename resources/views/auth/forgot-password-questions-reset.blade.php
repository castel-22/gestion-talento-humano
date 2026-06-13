<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/10 flex items-center justify-center mx-auto mb-4 shadow-sm">
            <i class="fas fa-check-shield text-2xl text-emerald-500"></i>
        </div>
        <h2 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.3em] mb-1">Identidad Verificada</h2>
        <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest opacity-60">Paso 3 de 3 — Establece tu nueva clave</p>
    </div>

    <form method="POST" action="{{ route('password.questions.update') }}" class="space-y-5">
        @csrf

        {{-- Nueva Contraseña --}}
        <div class="group" x-data="{ show: false }">
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest group-focus-within:text-pc-orange transition-colors">
                    Nueva Clave de Encriptación
                </label>
                <button type="button" @click="show = !show" class="text-[8px] font-black text-pc-orange hover:text-pc-blue uppercase tracking-widest transition-colors">
                    <span x-show="!show"><i class="fas fa-eye"></i> Ver</span>
                    <span x-show="show" x-cloak><i class="fas fa-eye-slash"></i> Ocultar</span>
                </button>
            </div>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-fingerprint text-gray-300 dark:text-gray-600 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input id="password"
                       class="input-auth-pc pl-12"
                       :type="show ? 'text' : 'password'"
                       name="password"
                       required
                       autofocus
                       autocomplete="new-password"
                       placeholder="••••••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmar Contraseña --}}
        <div class="group" x-data="{ show2: false }">
            <div class="flex justify-between items-center mb-2">
                <label for="password_confirmation" class="block text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest group-focus-within:text-pc-orange transition-colors">
                    Confirmar Nueva Clave
                </label>
                <button type="button" @click="show2 = !show2" class="text-[8px] font-black text-pc-orange hover:text-pc-blue uppercase tracking-widest transition-colors">
                    <span x-show="!show2"><i class="fas fa-eye"></i> Ver</span>
                    <span x-show="show2" x-cloak><i class="fas fa-eye-slash"></i> Ocultar</span>
                </button>
            </div>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-300 dark:text-gray-600 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input id="password_confirmation"
                       class="input-auth-pc pl-12"
                       :type="show2 ? 'text' : 'password'"
                       name="password_confirmation"
                       required
                       autocomplete="new-password"
                       placeholder="••••••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-auth-pc flex items-center justify-center gap-3 group" style="background: linear-gradient(90deg, #059669 0%, #10b981 100%); box-shadow: 0 10px 20px -5px rgba(5, 150, 105, 0.4);">
                <span>Guardar Nueva Contraseña</span>
                <i class="fas fa-shield-check text-[10px] group-hover:scale-110 transition-transform opacity-80"></i>
            </button>
        </div>
    </form>
</x-guest-layout>