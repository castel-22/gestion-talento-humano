@props(['status'])

@php
    $config = [
        'pendiente'    => ['bg' => 'bg-amber-50',    'text' => 'text-amber-700',   'border' => 'border-amber-200', 'icon' => 'fas fa-clock',           'label' => 'Pendiente',     'dot' => 'bg-amber-400'],
        'aprobado'     => ['bg' => 'bg-emerald-50',   'text' => 'text-emerald-700',  'border' => 'border-emerald-200','icon' => 'fas fa-check-circle',    'label' => 'Aprobado',      'dot' => 'bg-emerald-400'],
        'en_curso'     => ['bg' => 'bg-blue-50',      'text' => 'text-blue-700',     'border' => 'border-blue-200',  'icon' => 'fas fa-plane-departure', 'label' => 'En Curso',      'dot' => 'bg-blue-400 animate-pulse'],
        'interrumpido' => ['bg' => 'bg-orange-50',    'text' => 'text-orange-700',   'border' => 'border-orange-200','icon' => 'fas fa-pause-circle',    'label' => 'Interrumpido',  'dot' => 'bg-orange-400 animate-pulse'],
        'reanudado'    => ['bg' => 'bg-teal-50',      'text' => 'text-teal-700',     'border' => 'border-teal-200',  'icon' => 'fas fa-redo-alt',        'label' => 'Reanudado',     'dot' => 'bg-teal-400'],
        'finalizado'   => ['bg' => 'bg-gray-50',      'text' => 'text-gray-600',     'border' => 'border-gray-200',  'icon' => 'fas fa-flag-checkered',  'label' => 'Finalizado',    'dot' => 'bg-gray-400'],
        'rechazado'    => ['bg' => 'bg-red-50',       'text' => 'text-red-700',      'border' => 'border-red-200',   'icon' => 'fas fa-times-circle',    'label' => 'Rechazado',     'dot' => 'bg-red-400'],
    ];
    $c = $config[$status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'icon' => 'fas fa-question-circle', 'label' => ucfirst($status), 'dot' => 'bg-gray-400'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $c['dot'] }}"></span>
    <i class="{{ $c['icon'] }} text-[8px]"></i>
    {{ $c['label'] }}
</span>