<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { 
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 2px solid #c0392b;
            padding-bottom: 10px;
        }
        .section { 
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title { 
            background: #c0392b;
            color: white;
            padding: 8px;
            margin-bottom: 10px;
            font-weight: bold;
            border-radius: 4px;
        }
        .content { 
            margin-left: 15px;
            text-align: justify;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }
        .badge-success { background: #28a745; }
        .badge-info { background: #17a2b8; }
        .badge-warning { background: #ffc107; color: #000; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        ul {
            margin: 0;
            padding-left: 20px;
        }
        .coordenadas {
            color: #666;
            font-style: italic;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin: 5px 0;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DETALLADO #{{ $reporte->id_reporte }}</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section">
        <div class="section-title">INFORMACIÓN GENERAL</div>
        <div class="content">
            <p><strong>ID Reporte:</strong> {{ $reporte->id_reporte }}</p>
            <p><strong>Categoría:</strong> {{ $reporte->categoria->nombre ?? 'Sin categoría' }}</p>
            <p><strong>Fecha del Sistema:</strong> {{ Carbon\Carbon::parse($reporte->fecha_sistema)->format('d/m/Y H:i') }}</p>
            <p><strong>Fecha del Evento:</strong> {{ Carbon\Carbon::parse($reporte->fecha_evento)->format('d/m/Y H:i') }}</p>
            <p><strong>Lugar:</strong> {{ $reporte->lugar }}</p>
            <p><strong>Estado:</strong> 
                <span class="badge {{ $reporte->estado == 'aprobado' ? 'badge-success' : 'badge-info' }}">
                    {{ ucfirst($reporte->estado) }}
                </span>
            </p>
            @if($reporte->id_categoria == 6)
                @if($reporte->latitud && $reporte->longitud)
                    <p class="coordenadas"><strong>Coordenadas:</strong> {{ $reporte->latitud }}, {{ $reporte->longitud }}</p>
                @endif
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">DESCRIPCIÓN DEL HECHO</div>
        <div class="content">
            {{ $reporte->descripcion }}
        </div>
    </div>

    @if($reporte->actores_identificados)
    <div class="section">
        <div class="section-title">ACTORES IDENTIFICADOS</div>
        <div class="content">
            @if(strpos($reporte->actores_identificados, ';') !== false)
                <ul>
                    @foreach(explode(';', $reporte->actores_identificados) as $actor)
                        @if(trim($actor))
                            <li>{{ trim($actor) }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                {{ $reporte->actores_identificados }}
            @endif
        </div>
    </div>
    @endif

    @if($reporte->id_categoria == 6)
        @if($reporte->numero_personas || $reporte->presencia_autoridades || $reporte->intervencion_serenazgo)
        <div class="section">
            <div class="section-title">DETALLES DE LA SITUACIÓN</div>
            <div class="content">
                @if($reporte->numero_personas)
                    <p><strong>Número de Personas Estimado:</strong> {{ $reporte->numero_personas }}</p>
                @endif
                @if($reporte->presencia_autoridades)
                    <p><strong>Presencia de Autoridades:</strong> {{ $reporte->presencia_autoridades }}</p>
                @endif
                @if($reporte->intervencion_serenazgo)
                    <p><strong>Intervención Serenazgo/PNP:</strong> {{ $reporte->intervencion_serenazgo }}</p>
                @endif
            </div>
        </div>
        @endif
    @else
        @if($reporte->tema_tratado)
        <div class="section">
            <div class="section-title">TEMA TRATADO</div>
            <div class="content">
                {{ $reporte->tema_tratado }}
            </div>
        </div>
        @endif

        @if($reporte->acuerdos_compromisos)
        <div class="section">
            <div class="section-title">ACUERDOS Y COMPROMISOS</div>
            <div class="content">
                {{ $reporte->acuerdos_compromisos }}
            </div>
        </div>
        @endif
    @endif

    @if($reporte->recomendacion_preliminar)
    <div class="section">
        <div class="section-title">RECOMENDACIÓN PRELIMINAR</div>
        <div class="content">
            {{ $reporte->recomendacion_preliminar }}
        </div>
    </div>
    @endif

    @if($reporte->archivos && count($reporte->archivos) > 0)
    <div class="section">
        <div class="section-title">ENLACES RELACIONADOS</div>
        <div class="content">
            @foreach($reporte->archivos as $archivo)
                @if($archivo->tipo == 'enlace')
                    <div class="alert alert-info">
                        <strong>Enlace:</strong> {{ $archivo->url }}
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($respuestaExistente) && $respuestaExistente)
    <div class="section">
        <div class="section-title">OBSERVACIONES DEL ASOCIADO</div>
        <div class="content">
            <p><strong>Fecha de Respuesta:</strong> {{ Carbon\Carbon::parse($respuestaExistente->fecha_respuesta)->format('d/m/Y H:i') }}</p>
            <p><strong>Observación:</strong> {{ $respuestaExistente->respuesta }}</p>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>DECSAC - Sistema de Monitoreo Social</p>
        <p>Este documento fue generado automáticamente - {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Página 1</p>
    </div>
</body>
</html>