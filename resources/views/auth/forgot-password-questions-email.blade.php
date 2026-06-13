<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        ¿Olvidaste tu contraseña? Ingresa tu correo electrónico y te haremos unas preguntas de seguridad para verificarlo.
    </div>

    <form method="POST" action="{{ route('password.questions.verify-email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Verificar correo
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> 
