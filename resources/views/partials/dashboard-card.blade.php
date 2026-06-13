<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition relative overflow-hidden border-t-4 border-pc-orange">
    <!-- Pestaña esquina superior izquierda (icono más pequeño) -->
    <div class="absolute top-0 left-0 w-10 h-10 {{ $color }} rounded-br-lg flex items-center justify-center">
        <i class="fas {{ $icon }} text-white text-lg"></i>
    </div>
    <!-- Contenido desplazado a la derecha -->
    <div class="pl-14 pr-4 py-3">
        <p class="text-gray-500 text-xs font-medium uppercase tracking-wide" title="{{ $title }}">
            {{ $abbr ?? $title }}
        </p>
        @if($value > 0)
            <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
        @else
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $badge ?? 'Sin datos' }}</span>
        @endif
    </div>
</div>