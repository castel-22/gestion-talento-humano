<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xs font-black text-pc-orange uppercase tracking-[0.3em] mb-1">Recuperación de Acceso</h2>
        <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest opacity-60">Selecciona el método de verificación</p>
    </div>

    <div class="space-y-4">
        {{-- Opción 1: Preguntas de Seguridad --}}
        <a href="{{ route('password.questions.email') }}"
           class="group flex items-center gap-5 p-5 rounded-2xl border transition-all duration-300 cursor-pointer
                  bg-white/60 dark:bg-white/5 border-gray-100 dark:border-white/10
                  hover:border-pc-orange hover:shadow-[0_8px_30px_rgba(249,115,22,0.15)] hover:-translate-y-1">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 transition-all duration-300
                        bg-gradient-to-br from-pc-orange/10 to-orange-50 dark:from-pc-orange/20 dark:to-orange-900/10
                        group-hover:from-pc-orange group-hover:to-orange-500 shadow-sm">
                <i class="fas fa-shield-question text-xl text-pc-orange group-hover:text-white transition-colors duration-300"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black text-pc-blue dark:text-white uppercase tracking-widest mb-1 group-hover:text-pc-orange transition-colors">Preguntas de Seguridad</p>
                <p class="text-[9px] font-semibold text-gray-400 dark:text-gray-500 leading-relaxed">Verifica tu identidad respondiendo tus preguntas personales. Acceso inmediato sin espera.</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 group-hover:text-pc-orange group-hover:translate-x-1 transition-all duration-300 flex-shrink-0"></i>
        </a>

        {{-- Opción 2: Correo Electrónico --}}
        <a href="{{ route('password.request') }}"
           class="group flex items-center gap-5 p-5 rounded-2xl border transition-all duration-300 cursor-pointer
                  bg-white/60 dark:bg-white/5 border-gray-100 dark:border-white/10
                  hover:border-pc-blue hover:shadow-[0_8px_30px_rgba(11,59,94,0.12)] hover:-translate-y-1">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 transition-all duration-300
                        bg-gradient-to-br from-pc-blue/10 to-blue-50 dark:from-pc-blue/20 dark:to-blue-900/10
                        group-hover:from-pc-blue group-hover:to-blue-700 shadow-sm">
                <i class="fas fa-envelope-open-text text-xl text-pc-blue group-hover:text-white transition-colors duration-300"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black text-pc-blue dark:text-white uppercase tracking-widest mb-1 group-hover:text-pc-blue dark:group-hover:text-blue-300 transition-colors">Correo Electrónico</p>
                <p class="text-[9px] font-semibold text-gray-400 dark:text-gray-500 leading-relaxed">Recibirás un enlace seguro de recuperación en tu correo electrónico institucional.</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 group-hover:text-pc-blue group-hover:translate-x-1 transition-all duration-300 flex-shrink-0 dark:group-hover:text-blue-300"></i>
        </a>
    </div>

    <div class="mt-8 text-center border-t border-gray-100 dark:border-white/5 pt-6">
        <a href="{{ route('login') }}"
           class="text-[9px] font-black text-gray-400 dark:text-gray-500 hover:text-pc-orange uppercase tracking-widest transition-colors flex items-center justify-center gap-2 group">
            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            Volver al inicio de sesión
        </a>
    </div>
</x-guest-layout>
