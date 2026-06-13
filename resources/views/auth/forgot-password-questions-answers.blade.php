<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Responde las siguientes preguntas de seguridad para verificar tu identidad.
    </div>

    <form method="POST" action="{{ route('password.questions.verify-answers') }}">
        @csrf

        @foreach($answers as $index => $answer)
            <div class="mt-4">
<x-input-label :value="$answer->question ? $answer->question->question : 'Pregunta no disponible'" />
                <x-text-input class="block mt-1 w-full" type="text" name="answers[{{ $index }}]" required autofocus="{{ $index == 0 ? 'true' : 'false' }}" />
            </div>
        @endforeach

        <x-input-error :messages="$errors->get('answers')" class="mt-2" />

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Verificar respuestas
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>