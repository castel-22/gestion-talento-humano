<div class="bg-white rounded-lg shadow-md p-4">
    <h3 class="text-lg font-semibold mb-3 text-pc-blue">Guardias de Hoy</h3>
    @if(count($guardiasHoy) > 0)
        <div class="space-y-3">
            @foreach($guardiasHoy as $guardia)
                <div class="border-b pb-2 last:border-b-0">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">{{ $guardia['rotation'] }}</span>
                        <span class="inline-block w-6 h-6 text-center text-white text-sm font-bold rounded-full
                            {{ $guardia['letter'] === 'A' ? 'bg-pc-red' : ($guardia['letter'] === 'B' ? 'bg-pc-blue' : ($guardia['letter'] === 'C' ? 'bg-green-500' : 'bg-pc-orange')) }}">
                            {{ $guardia['letter'] }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-700 mt-1">
                        @if($guardia['employee'])
                            <i class="fas fa-user-check text-green-600 mr-1"></i> {{ $guardia['employee']->full_name }}
                            @if($guardia['employee']->position)
                                <span class="text-gray-500 text-xs block">{{ $guardia['employee']->position->name ?? '' }}</span>
                            @endif
                        @else
                            <span class="text-yellow-600"><i class="fas fa-exclamation-triangle mr-1"></i> Sin asignar</span>
                        @endif
                    </div>
                    @if($guardia['notes'])
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-pencil-alt mr-1"></i>{{ $guardia['notes'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center py-2">No hay guardias activas hoy.</p>
    @endif
</div>