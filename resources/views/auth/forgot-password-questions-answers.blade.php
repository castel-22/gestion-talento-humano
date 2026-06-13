<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pc-orange/10 to-orange-50 dark:from-pc-orange/20 dark:to-orange-900/10 flex items-center justify-center mx-auto mb-4 shadow-sm">
            <i class="fas fa-lock-open text-2xl text-pc-orange"></i>
        </div>
        <h2 class="text-xs font-black text-pc-orange uppercase tracking-[0.3em] mb-1">Preguntas de Seguridad</h2>
        <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest opacity-60">Paso 2 de 3 — Responde correctamente para continuar</p>
    </div>

    <form method="POST" action="{{ route('password.questions.verify-answers') }}" class="space-y-4">
        @csrf

        @if($errors->has('answers'))
            <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 flex items-center gap-3">
                <i class="fas fa-circle-exclamation text-red-500 flex-shrink-0"></i>
                <p class="text-[9px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest">{{ $errors->first('answers') }}</p>
            </div>
        @endif

        @foreach($answers as $index => $answer)
        <div class="group">
            <label class="block text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2 group-focus-within:text-pc-orange transition-colors">
                <span class="text-pc-orange mr-1">{{ $index + 1 }}.</span>
                {{ $answer->question ? $answer->question->question : 'Pregunta no disponible' }}
            </label>
            <div class="relative">
                <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-key text-gray-300 dark:text-gray-600 group-focus-within:text-pc-orange transition-colors"></i>
                </div>
                <input class="input-auth-pc pl-12"
                       type="text"
                       name="answers[{{ $index }}]"
                       required
                       {{ $index == 0 ? 'autofocus' : '' }}
                       placeholder="Tu respuesta secreta..." />
            </div>
        </div>
        @endforeach

        <div class="pt-4">
            <button type="submit" class="btn-auth-pc flex items-center justify-center gap-3 group">
                <span>Verificar Respuestas</span>
                <i class="fas fa-check text-[10px] group-hover:scale-110 transition-transform opacity-70"></i>
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