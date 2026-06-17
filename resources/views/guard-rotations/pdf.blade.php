<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Roll de Guardia - {{ $guardRotation->name }} {{ $date->format('m-Y') }}</title>
    <style>
        /* ── Ajustes para forzar a 1 página horizontal ── */
        @page { margin: 15px 20px; }
        
        /* ── Estilo Ejecutivo Minimalista ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 8.5pt; color: #1f2937; background: #fff; line-height: 1.2; }

        /* ── Header Institucional ── */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 2px solid #0B3B5E; padding-bottom: 5px; margin-bottom: 10px; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .header-text { font-size: 8.5pt; font-weight: bold; color: #0B3B5E; line-height: 1.2; text-transform: uppercase; text-align: center; }
        
        /* ── Bloque de Título ── */
        .title-block { text-align: center; border: 1.5px solid #0B3B5E; background-color: #f7fafc; padding: 4px; margin-bottom: 10px; }
        .title-block h2 { margin: 0; font-size: 13pt; font-weight: 800; color: #0B3B5E; letter-spacing: 0.05em; }
        .title-block h3 { margin: 1px 0 0; font-size: 10pt; color: #4a5568; font-weight: 700; text-transform: uppercase; }
        .title-block p { margin: 1px 0 0; font-size: 8pt; font-weight: bold; font-family: monospace; color: #6b7280; }

        /* ── Calendario de Guardia ── */
        .calendar-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .calendar-table th, .calendar-table td { border: 1px solid #cbd5e1; padding: 3px 2px; text-align: center; vertical-align: top; height: 35px; }
        .calendar-table th { background-color: #0B3B5E; color: #ffffff; font-weight: 700; font-size: 8pt; height: auto; padding: 3px 2px; text-transform: uppercase; }
        .day-number { font-weight: 800; font-size: 9pt; color: #1e293b; text-align: right; margin-bottom: 1px; }
        .letter-container { text-align: center; margin-top: 1px; }
        .letter { font-weight: 900; font-size: 11pt; display: inline-block; padding: 1px 4px; }
        
        .letter-A { color: #dc2626; }
        .letter-B { color: #2563eb; }
        .letter-C { color: #059669; }
        .letter-D { color: #d97706; }
        
        /* ── Bloque de Notas y Codificación ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; border: none; }
        .info-table td { border: none; padding: 0; vertical-align: top; }
        .note { border-left: 3px solid #2563eb; background-color: #eff6ff; padding: 6px 10px; font-size: 7.5pt; color: #1e40af; line-height: 1.3; }
        .codification { border: 1px solid #d1d5db; background-color: #f9fafb; padding: 6px 10px; font-size: 7.5pt; line-height: 1.3; }
        .codification-title { font-weight: 800; margin-bottom: 3px; color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; text-transform: uppercase; }
        
        /* ── Firmas ── */
        .signatures-table { width: 100%; border-collapse: collapse; margin-top: 20px; border: none; }
        .signatures-table td { border: none; padding: 0; text-align: center; vertical-align: top; font-size: 7.5pt; font-weight: 700; color: #374151; line-height: 1.3; text-transform: uppercase; }
        .signature-line { width: 85%; margin: 0 auto 3px; border: 0; border-top: 1px solid #111827; }
    </style>
</head>
<body>
    <!-- Encabezado con Logos -->
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                <img src="{{ public_path('images/logo_pc.png') }}" style="height: 55px; width: auto;">
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
                <img src="{{ public_path('images/logo_ciudad_bolivar.png') }}" style="height: 55px; width: auto;">
            </td>
        </tr>
    </table>

    <!-- Bloque de Título -->
    <div class="title-block">
        <h2>ROLL DE GUARDIA 24X72</h2>
        <h3>{{ $guardRotation->name }}</h3>
        <p>PERÍODO: {{ $date->format('d/m/Y') }} AL {{ $date->copy()->endOfMonth()->format('d/m/Y') }}</p>
    </div>

    <!-- Calendario de Guardia -->
    <table class="calendar-table">
        <thead>
            <tr>
                <th width="14.28%">LUNES</th>
                <th width="14.28%">MARTES</th>
                <th width="14.28%">MIÉRCOLES</th>
                <th width="14.28%">JUEVES</th>
                <th width="14.28%">VIERNES</th>
                <th width="14.28%">SÁBADO</th>
                <th width="14.28%">DOMINGO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeks as $week)
            <tr>
                @foreach($week as $day)
                    @if($day)
                        @php $duty = $duties[$day] ?? null; @endphp
                        <td>
                            <div class="day-number">{{ $day }}</div>
                            @if($duty)
                                <div class="letter-container">
                                    <span class="letter letter-{{ $duty->letter }}">{{ $duty->letter }}</span>
                                </div>
                            @endif
                        </td>
                    @else
                        <td style="background-color: #f8fafc;"></td>
                    @endif
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bloque de Notas y Codificación -->
    <table class="info-table">
        <tr>
            <td style="width: 55%; padding-right: 15px;">
                <div class="note">
                    <strong>NOTA IMPORTANTE:</strong><br>
                    • La entrada a la guardia es a las 07:00 HLV y la entrega de forma presencial es a las 07:00 HLV.<br>
                    • Ningún funcionario debe retirarse sin haber entregado formalmente la guardia a su relevo.<br>
                    • La letra asignada representa la guardia de 24 HORAS correspondiente.
                </div>
            </td>
            <td style="width: 45%;">
                <div class="codification">
                    <div class="codification-title">Técnicos de Guardia Asignados</div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 7.5pt; margin-top: 3px;">
                        @foreach(['A','B','C','D'] as $letter)
                            @php $employee = $letterEmployees[$letter] ?? null; @endphp
                            <tr>
                                <td style="width: 15%; font-weight: bold; padding: 2px 0;">Grupo {{ $letter }}:</td>
                                <td style="width: 85%; padding: 2px 0; border-bottom: 1px dotted #ccc;">{{ $employee ? $employee->full_name : '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Firmas Autorizadas -->
    <table class="signatures-table">
        <tr>
            <td style="width: 30%;">
                <hr class="signature-line">
                DIRECTOR DE PROTECCIÓN CIVIL<br>Y ADMINISTRACIÓN DE DESASTRES
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 30%;">
                <hr class="signature-line">
                SUB-DIRECTOR DE PROTECCIÓN CIVIL<br>Y ADMINISTRACIÓN DE DESASTRES
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 30%;">
                <hr class="signature-line">
                JEFE DE DIVISIÓN OPERATIVA<br>DE PROTECCIÓN CIVIL ESTADAL
            </td>
        </tr>
    </table>
</body>
</html>