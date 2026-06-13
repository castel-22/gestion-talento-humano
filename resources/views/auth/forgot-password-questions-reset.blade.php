<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Tus respuestas son correctas. Ahora puedes ingresar tu nueva contraseña.
    </div>

    <form method="POST" action="{{ route('password.questions.update') }}" class="space-y-6">
        @csrf

        <!-- Nueva Contraseña -->
        <div>
            <x-input-label for="password" :value="__('Nueva Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autofocus autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Nueva Contraseña -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button class="px-6 py-2">
                Actualizar contraseña
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>