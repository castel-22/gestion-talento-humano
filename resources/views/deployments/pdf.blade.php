<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Despliegue {{ $deployment->id }}</title>
    <style>
        /* ── Estilo Ejecutivo Minimalista ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9pt; color: #1f2937; background: #fff; line-height: 1.4; padding: 20px; }

        /* ── Header Institucional ── */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 2px solid #0B3B5E; padding-bottom: 10px; margin-bottom: 15px; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .header-text { font-size: 9pt; font-weight: bold; color: #0B3B5E; line-height: 1.3; text-transform: uppercase; text-align: center; }
        
        /* ── Tipografía ── */
        h1.main-title { font-size: 16pt; font-weight: 800; color: #111827; margin-bottom: 15px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        h2.block-title { font-size: 12pt; font-weight: 700; color: #1f2937; margin-top: 25px; margin-bottom: 10px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        
        /* ── Tablas ── */
        table.md-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8.5pt; }
        table.md-table th { border-bottom: 2px solid #9ca3af; padding: 6px 8px; text-align: left; font-weight: 700; color: #374151; text-transform: uppercase; font-size: 7.5pt; background-color: #f3f4f6; width: 25%; }
        table.md-table td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; color: #1f2937; vertical-align: top; }
        table.md-table tr:nth-child(even) td { background-color: #f9fafb; }
        
        /* ── Listas (Tabla vertical) ── */
        table.v-table th { width: auto; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                <img src="{{ public_path('images/logo_pc.png') }}" alt="Protección Civil" style="height: 65px; width: auto;">
            </td>
            <td style="width: 70%;">
                <div class="header-text">
                    REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
                    GOBERNACIÓN DEL ESTADO BOLÍVAR<br>
                    SECRETARÍA DE SEGURIDAD CIUDADANA<br>
                    DIRECCIÓN ESTADAL DE PROTECCIÓN CIVIL Y ADMINISTRACIÓN DE DESASTRES
                </div>
            </td>
            <td style="width: 15%; text-align: right;">
                <img src="{{ public_path('images/logo_ciudad_bolivar.png') }}" alt="Gobernación" style="height: 65px; width: auto;">
            </td>
        </tr>
    </table>

    <h1 class="main-title">Orden de Despliegue Operativo</h1>
    
    <div style="font-family: monospace; font-size: 8pt; color: #4b5563; background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px; margin-bottom: 15px; display: inline-block;">
        <strong>ID DESPLIEGUE:</strong> {{ str_pad($deployment->id, 5, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp; <strong>FECHA EMISIÓN:</strong> {{ now()->format('d/m/Y h:i A') }}
    </div>

    <h2 class="block-title">1. Detalles de la Operación</h2>
    <table class="md-table">
        <tr><th>Lugar / Ubicación</th><td>{{ $deployment->place }}</td></tr>
        <tr><th>División Responsable</th><td>{{ $deployment->division ?? '—' }}</td></tr>
        <tr><th>Motivo del Despliegue</th><td>{{ $deployment->reason }}</td></tr>
        <tr><th>Comandante / Responsable</th><td><strong>{{ $deployment->supervisor->full_name ?? '—' }}</strong></td></tr>
        <tr><th>Fecha y Hora de Inicio</th><td>{{ $deployment->start_datetime->format('d/m/Y H:i') }}</td></tr>
        <tr><th>Fecha y Hora de Fin</th><td>{{ $deployment->is_indefinite ? 'Indefinido' : ($deployment->end_datetime ? $deployment->end_datetime->format('d/m/Y H:i') : '—') }}</td></tr>
        @if($deployment->latitude && $deployment->longitude)
        <tr><th>Coordenadas GPS</th><td>{{ $deployment->latitude }}, {{ $deployment->longitude }}</td></tr>
        @endif
        @if($deployment->notes)
        <tr><th>Instrucciones Especiales</th><td>{{ $deployment->notes }}</td></tr>
        @endif
    </table>

    <h2 class="block-title">2. Personal Desplegado ({{ $deployment->participants->count() }} Funcionarios)</h2>
    @if($deployment->participants->isEmpty())
        <p style="font-style: italic; color: #6b7280; font-size: 8.5pt;">No se asignó personal a este despliegue.</p>
    @else
        <table class="md-table v-table">
            <thead>
                <tr>
                    <th width="35%">Funcionario</th>
                    <th width="15%">Cédula</th>
                    <th width="20%">Rol Asignado</th>
                    <th width="20%">División</th>
                    <th width="10%">Líder</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deployment->participants as $p)
                    <tr>
                        <td><strong>{{ $p->full_name }}</strong></td>
                        <td>{{ $p->id_number }}</td>
                        <td>{{ $p->pivot->role ?? '—' }}</td>
                        <td>{{ $p->pivot->division ?? '—' }}</td>
                        <td>{{ $p->pivot->is_leader ? 'Sí' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Firmas Institucionales --}}
    <table style="width: 100%; margin-top: 50px; border-collapse: collapse; border: none;">
        <tr>
            <td style="width: 45%; text-align: center; vertical-align: top; border: none;">
                <div style="border-top: 1px solid #111827; margin-top: 40px; padding-top: 5px; font-weight: 700; font-size: 8pt; text-transform: uppercase;">{{ $deployment->supervisor->full_name ?? 'Comandante del Despliegue' }}</div>
                <div style="font-size: 7pt; color: #6b7280; margin-top: 3px;">Responsable Principal</div>
            </td>
            <td style="width: 10%; border: none;"></td>
            <td style="width: 45%; text-align: center; vertical-align: top; border: none;">
                <div style="border-top: 1px solid #111827; margin-top: 40px; padding-top: 5px; font-weight: 700; font-size: 8pt; text-transform: uppercase;">Vo.Bo. Dirección General</div>
                <div style="font-size: 7pt; color: #6b7280; margin-top: 3px;">Protección Civil</div>
            </td>
        </tr>
    </table>
</body>
</html>