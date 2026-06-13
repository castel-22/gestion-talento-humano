<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Empleado</th>
            <th>Cédula</th>
            <th>Hora entrada</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $att)
        <tr>
            <td>{{ $att->date->format('d/m/Y') }}</td>
            <td>{{ $att->employee->full_name }}</td>
            <td>{{ $att->employee->id_number }}</td>
            <td>{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '—' }}</td>
            <td>{{ ucfirst($att->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>