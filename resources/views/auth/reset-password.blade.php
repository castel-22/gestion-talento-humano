<x-guest-layout>
    <div class="mb-8">
        <h2 class="outfit-font text-xl text-pc-blue uppercase tracking-tight mb-2 text-center">Nueva Contraseña</h2>
        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-relaxed text-center">
            Configure sus nuevas credenciales de acceso institucional.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Correo Electrónico -->
        <div class="group">
            <label for="email" class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-pc-orange transition-colors">Correo Institucional</label>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-300 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input id="email" 
                       class="input-auth-pc pl-12" 
                       type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="group">
            <label for="password" class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-pc-orange transition-colors">Nueva Contraseña</label>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-key text-gray-300 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input id="password" class="input-auth-pc pl-12" type="password" name="password" required autocomplete="new-password" placeholder="••••••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Contraseña -->
        <div class="group">
            <label for="password_confirmation" class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-pc-orange transition-colors">Reconfirmar Contraseña</label>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-check-double text-gray-300 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input id="password_confirmation" class="input-auth-pc pl-12" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-auth-pc">
                Restablecer Credenciales
            </button>
        </div>
    </form>
</x-guest-layout>
