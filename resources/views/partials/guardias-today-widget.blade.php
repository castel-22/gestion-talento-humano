<div class="card-pc p-4 dark:bg-slate-900 dark:border-slate-800">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-[11px] font-black text-pc-blue dark:text-white uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-shield-alt text-pc-orange"></i> Guardias del Día
        </h3>
        <a href="{{ route('guard-rotations.index') }}" class="text-[9px] font-black text-pc-blue dark:text-pc-orange hover:underline uppercase tracking-widest transition-colors">
            Ver Roles
        </a>
    </div>

    <div class="space-y-2">
        @forelse($guardiasHoy as $guardia)
            <div class="bg-gray-50 dark:bg-slate-800/50 rounded-xl p-2 border border-gray-100 dark:border-slate-800 flex items-center gap-3 group hover:bg-pc-blue/5 dark:hover:bg-pc-orange/5 transition-all">
                <div class="w-8 h-8 rounded-lg bg-pc-blue text-white flex flex-col items-center justify-center shadow-sm shrink-0">
                    <span class="text-[8px] font-bold opacity-70 uppercase leading-none mt-1">{{ $guardia['rotation'] }}</span>
                    <span class="text-sm font-black leading-tight">{{ $guardia['letter'] }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    @if($guardia['employee'])
                        <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $guardia['employee']->full_name }}</p>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 font-bold truncate uppercase tracking-tighter">
                            {{ $guardia['employee']->position ?? 'Personal de Guardia' }}
                        </p>
                    @else
                        <p class="text-xs font-bold text-gray-400 dark:text-slate-600 italic">Sin personal asignado</p>
                        <p class="text-[9px] text-pc-red font-black uppercase tracking-tighter">Acción requerida</p>
                    @endif
                </div>
                <div class="flex flex-col items-end">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                </div>
            </div>
            @if($guardia['notes'])
                <p class="text-[10px] text-gray-500 italic px-2 -mt-2 mb-2">
                    <i class="fas fa-sticky-note mr-1 opacity-50"></i> {{ $guardia['notes'] }}
                </p>
            @endif
        @empty
            <div class="text-center py-6">
                <div class="bg-gray-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-calendar-times text-gray-400"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium">No hay rotaciones activas programadas para hoy.</p>
            </div>
        @endforelse
    </div>
</div>
