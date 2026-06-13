@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">Detalle de Asistencia</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <dt class="font-bold">Fecha:</dt>
                    <dd>{{ $attendance->date->format('d/m/Y') }}</dd>
                    <dt class="font-bold">Empleado:</dt>
                    <dd>{{ $attendance->employee->full_name }}</dd>
                    <dt class="font-bold">Cédula:</dt>
                    <dd>{{ $attendance->employee->id_number }}</dd>
                    <dt class="font-bold">Hora de entrada:</dt>
                    <dd>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '—' }}</dd>
                    <dt class="font-bold">Estado:</dt>
                    <dd>
                        @if($attendance->status == 'present')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Presente</span>
                        @elseif($attendance->status == 'absent')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ausente</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($attendance->status) }}</span>
                        @endif
                    </dd>
                </dl>
                <div class="mt-4">
                    <a href="{{ route('attendances.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection