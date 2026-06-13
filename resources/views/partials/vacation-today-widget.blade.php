<div class="bg-white rounded-lg shadow-md p-4">
    <h3 class="text-lg font-semibold mb-3 text-pc-blue"><i class="fas fa-umbrella-beach mr-2"></i>De vacaciones hoy</h3>
    @if($employeesOnVacation->count())
        <ul class="divide-y divide-gray-200">
            @foreach($employeesOnVacation as $vacation)
                <li class="py-2">
                    <span class="font-medium">{{ $vacation->employee->full_name }}</span>
                    <span class="text-xs text-gray-500 block">
                        {{ $vacation->start_date->format('d/m/Y') }} - {{ $vacation->end_date->format('d/m/Y') }}
                    </span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500 text-sm">Nadie está de vacaciones hoy.</p>
    @endif
</div>