@props(['vacations', 'context' => 'pending'])

<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                @if(in_array($context, ['pending', 'approved']))
                    <th class="w-12 px-6 py-4 text-center">
                        <input type="checkbox" id="select-all-{{ $context }}" class="rounded border-gray-300 text-pc-orange focus:ring-pc-orange">
                    </th>
                @endif
                <th class="px-6 py-4 text-[10px] font-black text-pc-blue uppercase tracking-widest">Integrante</th>
                <th class="px-6 py-4 text-[10px] font-black text-pc-blue uppercase tracking-widest">Período de Disfrute</th>
                <th class="px-6 py-4 text-[10px] font-black text-pc-blue uppercase tracking-widest text-center">Días</th>
                @if($context === 'paused')
                    <th class="px-6 py-4 text-[10px] font-black text-pc-blue uppercase tracking-widest text-center">Remanente</th>
                    <th class="px-6 py-4 text-[10px] font-black text-pc-blue uppercase tracking-widest">Motivo</th>
                @endif
                <th class="px-6 py-4 text-[10px] font-black text-pc-blue uppercase tracking-widest text-center">Estado</th>
                <th class="px-6 py-4 text-[10px] font-black text-pc-blue uppercase tracking-widest text-right">Gestión</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 bg-white">
            @forelse($vacations as $vacation)
                @php
                    $isPaused = $vacation->status === \App\Models\Vacation::STATUS_INTERRUMPIDO;
                    $isResumed = $vacation->status === \App\Models\Vacation::STATUS_REANUDADO;
                    $rowHighlight = $isPaused ? 'border-l-4 border-l-orange-400 bg-orange-50/30' : ($isResumed ? 'border-l-4 border-l-teal-400 bg-teal-50/20' : '');
                @endphp
                <tr class="hover:bg-pc-blue/[0.02] transition-colors group {{ $rowHighlight }}">
                    @if(in_array($context, ['pending', 'approved']))
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" name="ids[]" value="{{ $vacation->id }}" class="vacation-checkbox rounded border-gray-300 text-pc-orange focus:ring-pc-orange">
                        </td>
                    @endif
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-pc-blue/5 text-pc-blue flex items-center justify-center font-black text-[10px] shadow-inner group-hover:bg-pc-orange group-hover:text-white transition-colors">
                                {{ strtoupper(substr($vacation->employee->first_name, 0, 1) . substr($vacation->employee->last_name, 0, 1)) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-[11px] font-black text-gray-800 uppercase leading-none truncate">{{ $vacation->employee->full_name }}</span>
                                <span class="text-[9px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">{{ $vacation->employee->position ?: 'Sin Cargo' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="inline-flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                            <span class="text-[10px] font-black text-gray-700">{{ $vacation->start_date->format('d/m/y') }}</span>
                            <i class="fas fa-chevron-right text-[8px] text-gray-300"></i>
                            <span class="text-[10px] font-black text-gray-700">{{ $vacation->end_date->format('d/m/y') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-black text-pc-blue">{{ $vacation->days_taken }}</span>
                            @if($vacation->accumulated_days_used > 0)
                                <div class="flex gap-1 mt-1">
                                    <span class="text-[8px] font-bold px-1.5 py-0.5 bg-blue-50 text-pc-blue rounded-md border border-blue-100" title="Días Regulares">
                                        R: {{ $vacation->days_taken - $vacation->accumulated_days_used }}
                                    </span>
                                    <span class="text-[8px] font-bold px-1.5 py-0.5 bg-orange-50 text-pc-orange rounded-md border border-orange-100" title="Días Acumulados">
                                        A: {{ $vacation->accumulated_days_used }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </td>
                    @if($context === 'paused')
                        <td class="px-6 py-4 text-center">
                            @if($isPaused && $vacation->remaining_days > 0)
                                <div class="flex flex-col items-center gap-1">
                                    <span class="bg-orange-100 text-orange-700 text-sm font-black px-3 py-1 rounded-lg border border-orange-200 inline-flex items-center gap-1">
                                        <i class="fas fa-hourglass-half text-[9px] animate-pulse"></i>
                                        {{ $vacation->remaining_days }}
                                    </span>
                                    <span class="text-[8px] text-gray-400 font-bold">días pendientes</span>
                                </div>
                            @elseif($isResumed)
                                <span class="bg-teal-100 text-teal-700 text-[10px] font-black px-2 py-1 rounded-lg border border-teal-200 inline-flex items-center gap-1">
                                    <i class="fas fa-check-double text-[8px]"></i> Completado
                                </span>
                            @else
                                <span class="text-[10px] text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($vacation->interruption_reason)
                                <div class="max-w-[200px]">
                                    <p class="text-[10px] text-gray-600 line-clamp-2 leading-snug" title="{{ $vacation->interruption_reason }}">
                                        <i class="fas fa-exclamation-triangle text-orange-400 mr-1 text-[8px]"></i>
                                        {{ $vacation->interruption_reason }}
                                    </p>
                                </div>
                            @else
                                <span class="text-[10px] text-gray-400">Sin motivo</span>
                            @endif
                        </td>
                    @endif
                    <td class="px-6 py-4 text-center">
                        @include('partials.vacation-status-badge', ['status' => $vacation->status])
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('vacations.show', $vacation) }}" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-pc-blue hover:bg-pc-blue hover:text-white transition-all shadow-sm" title="Ver detalle">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            
                            @if($context === 'pending')
                                <form action="{{ route('vacations.approve', $vacation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-green-500 hover:bg-green-500 hover:text-white transition-all shadow-sm" title="Aprobar">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('vacations.reject', $vacation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-pc-red hover:bg-pc-red hover:text-white transition-all shadow-sm" title="Rechazar">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </form>
                                @can('update', $vacation)
                                    <a href="{{ route('vacations.edit', $vacation) }}" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-gray-500 hover:bg-gray-500 hover:text-white transition-all shadow-sm" title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                @endcan
                            @endif

                            @if($context === 'approved' && $vacation->canBeInterrupted())
                                <button type="button" @click="openInterruptModal({{ $vacation->id }})" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-pc-orange hover:bg-pc-orange hover:text-white transition-all shadow-sm" title="Interrumpir">
                                    <i class="fas fa-pause text-xs"></i>
                                </button>
                            @endif

                            @if($context === 'paused' && $vacation->canBeResumed())
                                <a href="{{ route('vacations.resume.form', $vacation) }}" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-teal-500 hover:bg-teal-500 hover:text-white transition-all shadow-sm animate-pulse" title="Reanudar vacaciones">
                                    <i class="fas fa-play text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                <i class="fas fa-inbox text-gray-200 text-2xl"></i>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No se encontraron registros de vacaciones en esta sección</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>