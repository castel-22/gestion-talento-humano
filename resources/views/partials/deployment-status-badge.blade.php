@props(['status'])

@php
    $colors = [
        'programado'  => 'bg-blue-100 text-blue-800',
        'en_curso'    => 'bg-green-100 text-green-800',
        'finalizado'  => 'bg-gray-100 text-gray-800',
        'cancelado'   => 'bg-red-100 text-red-800',
    ];
    $labels = [
        'programado'  => 'Programado',
        'en_curso'    => 'En curso',
        'finalizado'  => 'Finalizado',
        'cancelado'   => 'Cancelado',
    ];
@endphp

<span class="px-2 py-1 text-xs rounded-full {{ $colors[$status] ?? 'bg-gray-100 text-gray-800' }}">
    {{ $labels[$status] ?? $status }}
</span>