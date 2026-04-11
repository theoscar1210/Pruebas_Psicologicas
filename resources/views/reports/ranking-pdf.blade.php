<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking — {{ $position->name }}</title>
    <style>
        {!! file_get_contents(public_path('css/pdf/base.css')) !!}
        {!! file_get_contents(public_path('css/pdf/reportes.css')) !!}
    </style>
</head>
<body>

    {{-- Marca de agua: se repite en cada página gracias a position:fixed --}}
    <div class="marca-agua">CONFIDENCIAL</div>

    {{-- ══ ENCABEZADO ══════════════════════════════════════════════════════════ --}}
    <div class="header">
        <div class="header-celda-logo">
            <span class="logo-circulo">MC</span>
            <span class="logo-textos">
                <div class="logo-nombre">MenteClara</div>
                <div class="logo-autor">by Emma Naranjo</div>
            </span>
        </div>
        <div class="header-celda-info">
            <div class="header-titulo">Ranking de Candidatos</div>
            <div class="header-subtitulo">Cargo: {{ $position->name }}</div>
            <div class="header-fecha">Generado: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ══ RESUMEN ESTADÍSTICO ══════════════════════════════════════════════════ --}}
    <div class="seccion">
        @php
            $total     = $candidates->count();
            $aprobaron = $candidates->where('all_passed', true)->count();
            $promedio  = $candidates->avg('avg_pct');
        @endphp
        <div class="resumen-estadistico">
            <div class="resumen-caja">
                <div class="resumen-numero">{{ $total }}</div>
                <div class="resumen-etiqueta">Candidatos evaluados</div>
            </div>
            <div class="resumen-caja" style="border-left:none">
                <div class="resumen-numero texto-verde">{{ $aprobaron }}</div>
                <div class="resumen-etiqueta">Aprobaron todas</div>
            </div>
            <div class="resumen-caja" style="border-left:none">
                <div class="resumen-numero texto-rojo">{{ $total - $aprobaron }}</div>
                <div class="resumen-etiqueta">No aprobaron</div>
            </div>
            <div class="resumen-caja" style="border-left:none">
                <div class="resumen-numero">{{ number_format($promedio, 1) }}%</div>
                <div class="resumen-etiqueta">Promedio general</div>
            </div>
        </div>
    </div>

    {{-- ══ TABLA DE RANKING ════════════════════════════════════════════════════ --}}
    <div class="seccion">
        <div class="seccion-titulo">Ranking por puntaje promedio</div>

        @if($candidates->isEmpty())
            <p style="color:#9ca3af;text-align:center;padding:20px 0">No hay candidatos con pruebas completadas para este cargo.</p>
        @else
        <table class="tabla-ranking">
            <thead>
                <tr>
                    <th style="width:35px">#</th>
                    <th>Candidato</th>
                    <th style="width:130px">Promedio</th>
                    <th style="width:70px;text-align:center">Pruebas</th>
                    <th style="width:80px;text-align:center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($candidates as $i => $fila)
                @php $puesto = $i + 1; @endphp
                <tr class="{{ $puesto <= 3 ? 'puesto-'.$puesto : '' }}">
                    <td style="text-align:center">
                        @if($puesto === 1)     <span class="medalla">🥇</span>
                        @elseif($puesto === 2) <span class="medalla">🥈</span>
                        @elseif($puesto === 3) <span class="medalla">🥉</span>
                        @else                  <span class="texto-gris">{{ $puesto }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $fila['candidate']->name }}</strong>
                        @if($fila['candidate']->document_number)
                            <br><span style="font-size:9px;color:#9ca3af">{{ $fila['candidate']->document_number }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="porcentaje {{ $fila['avg_pct'] >= 60 ? 'texto-verde' : 'texto-rojo' }}">
                            {{ $fila['avg_pct'] }}%
                        </span>
                        <span class="barra-contenedor">
                            <span class="barra-relleno-tabla" style="width:{{ $fila['avg_pct'] }}%;background:{{ $fila['avg_pct'] >= 60 ? '#16a34a' : '#dc2626' }}"></span>
                        </span>
                    </td>
                    <td style="text-align:center">{{ $fila['passed'] }}/{{ $fila['total_tests'] }}</td>
                    <td style="text-align:center">
                        @if($fila['all_passed'])
                            <span class="badge badge-aprobado">Aprobó</span>
                        @elseif($fila['total_tests'] === 0)
                            <span class="badge badge-pendiente">Sin pruebas</span>
                        @else
                            <span class="badge badge-reprobado">No aprobó</span>
                        @endif
                    </td>
                </tr>

                {{-- Detalle de cada prueba --}}
                @foreach($fila['candidate']->assignments as $asignacion)
                @if($asignacion->result)
                <tr>
                    <td></td>
                    <td colspan="4" style="padding-top:2px;padding-bottom:4px;color:#6b7280;font-size:9px;padding-left:20px">
                        ↳ {{ $asignacion->test->name }}:
                        <strong class="{{ $asignacion->result->passed ? 'texto-verde' : 'texto-rojo' }}">
                            {{ $asignacion->result->percentage }}%
                        </strong>
                        ({{ $asignacion->result->total_score }}/{{ $asignacion->result->max_score }} pts)
                        — {{ $asignacion->result->passed ? 'Aprobó' : 'No aprobó' }}
                    </td>
                </tr>
                @endif
                @endforeach

                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- ══ PIE DE PÁGINA ════════════════════════════════════════════════════════ --}}
    <div class="footer" style="margin:20px 28px 0;width:calc(100% - 56px)">
        <div class="footer-izquierda">MenteClara · <em>Donde el talento encuentra su medida</em> — Documento confidencial</div>
        <div class="footer-derecha">{{ now()->format('d/m/Y') }}</div>
    </div>

</body>
</html>
