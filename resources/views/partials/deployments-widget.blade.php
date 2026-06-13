<div class="bg-white rounded-lg shadow-md p-4">
    <h3 class="text-lg font-semibold mb-3 text-pc-blue"><i class="fas fa-truck mr-2"></i>Despliegues activos</h3>
    @if($activeDeployments->count())
        <ul class="divide-y divide-gray-200">
            @foreach($activeDeployments as $d)
                <li class="py-2">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $d->place }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800">En curso</span>
                    </div>
                    <div class="text-xs text-gray-500">{{ $d->start_datetime->format('d/m/Y H:i') }} – {{ $d->is_indefinite ? 'Indefinido' : ($d->end_datetime ? $d->end_datetime->format('H:i') : '') }}</div>
                    <div class="text-xs text-gray-600 mt-1">👥 {{ $d->participants->count() }} participantes</div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-500">No hay despliegues activos ahora.</p>
    @endif
</div>