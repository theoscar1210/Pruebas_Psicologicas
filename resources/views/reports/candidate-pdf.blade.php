<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte — {{ $candidate->name }}</title>
    <style>
        {{-- safe: inlined desde archivos CSS locales del servidor, no datos de usuario --}}
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
            <div class="header-titulo">Reporte de Candidato</div>
            <div class="header-subtitulo">Sistema de Evaluación Psicológica · RRHH</div>
            <div class="header-fecha">Generado: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ══ INFORMACIÓN DEL CANDIDATO ════════════════════════════════════════════ --}}
    <div class="cuadro-info">
        <div class="info-celda">
            <div class="info-etiqueta">Candidato</div>
            <div class="info-valor">{{ $candidate->name }}</div>
            @if($candidate->document_number)
                <div style="font-size:10px;color:#6b7280;margin-top:3px">{{ $candidate->document_number }}</div>
            @endif
        </div>
        <div class="info-celda" style="border-left:none">
            <div class="info-etiqueta">Cargo</div>
            <div class="info-valor">{{ $candidate->position?->name ?? 'Sin asignar' }}</div>
            @if($candidate->email)
                <div style="font-size:10px;color:#6b7280;margin-top:3px">{{ $candidate->email }}</div>
            @endif
        </div>
    </div>

    {{-- ══ PRUEBAS COMPLETADAS ══════════════════════════════════════════════════ --}}
    @forelse($candidate->assignments->where('status', 'completed') as $i => $asignacion)

        @if($i > 0)
            <div class="salto-pagina"></div>
            <div style="height:20px"></div>
        @endif

        <div class="seccion">
            <div class="seccion-titulo">{{ $asignacion->test->name }}</div>

            {{-- Banner de resultado --}}
            @if($asignacion->result)
            @php $r = $asignacion->result; @endphp
            <div class="banner-resultado {{ $r->passed ? 'banner-aprobado' : 'banner-reprobado' }}">
                <table style="border:none">
                    <tr>
                        <td style="border:none;padding:0;width:120px">
                            <div class="puntaje-grande {{ $r->passed ? 'puntaje-aprobado' : 'puntaje-reprobado' }}">{{ $r->percentage }}%</div>
                            <div class="resultado-etiqueta">Puntaje obtenido</div>
                        </td>
                        <td style="border:none;padding:0">
                            <div class="resultado-detalle">
                                <strong>{{ $r->total_score }} / {{ $r->max_score }} puntos</strong><br>
                                Mínimo requerido: {{ $asignacion->test->passing_score }}%<br>
                                Resultado: <strong>{{ $r->passed ? '✓ Aprobado' : '✗ No aprobado' }}</strong>
                            </div>
                        </td>
                        <td style="border:none;padding:0;text-align:right;vertical-align:top;font-size:10px;color:#6b7280">
                            @if($asignacion->started_at)
                                Iniciada: {{ $asignacion->started_at->format('d/m/Y H:i') }}<br>
                            @endif
                            @if($asignacion->completed_at)
                                Finalizada: {{ $asignacion->completed_at->format('d/m/Y H:i') }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Detalle por pregunta --}}
            <table>
                <thead>
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-texto">Pregunta</th>
                        <th class="col-resp">Respuesta</th>
                        <th class="col-puntos">Pts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asignacion->test->questions as $pregunta)
                    @php $respuesta = $asignacion->answers->firstWhere('question_id', $pregunta->id); @endphp
                    <tr>
                        <td class="col-num">{{ $pregunta->order }}</td>
                        <td class="col-texto">{{ $pregunta->text }}</td>
                        <td class="col-resp">
                            @if(!$respuesta)
                                <span style="color:#9ca3af;font-style:italic">Sin responder</span>
                            @elseif($pregunta->isOpen())
                                <span class="badge badge-pendiente">Respuesta abierta</span>
                                @if($respuesta->text_answer)
                                    <br><span style="color:#6b7280;font-style:italic">{{ Str::limit($respuesta->text_answer, 80) }}</span>
                                @endif
                            @else
                                {{ $respuesta->option?->text ?? '—' }}
                            @endif
                        </td>
                        <td class="col-puntos">
                            @if($respuesta && !$pregunta->isOpen())
                                <span class="{{ $respuesta->score > 0 ? 'puntos-ok' : 'puntos-no' }}">
                                    {{ number_format($respuesta->score, 1) }}
                                </span>
                                / {{ $pregunta->points }}
                            @elseif($pregunta->isOpen())
                                <span class="texto-gris">—</span>
                            @else
                                <span class="puntos-no">0</span> / {{ $pregunta->points }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @empty
        <div class="seccion" style="text-align:center;color:#9ca3af;padding:30px 0">
            Este candidato no tiene pruebas completadas aún.
        </div>
    @endforelse

    {{-- ══ PIE DE PÁGINA ════════════════════════════════════════════════════════ --}}
    <div class="footer" style="margin:20px 28px 0;width:calc(100% - 56px)">
        <div class="footer-izquierda">MenteClara · <em>Donde el talento encuentra su medida</em> — Documento confidencial</div>
        <div class="footer-derecha">{{ now()->format('d/m/Y') }}</div>
    </div>

</body>
</html>
