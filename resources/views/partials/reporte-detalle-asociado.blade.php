<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold">Información General</h6>
        <p><strong>ID Reporte:</strong> {{ $reporte->id_reporte }}</p>
        <p><strong>Categoría:</strong> {{ $reporte->categoria->nombre ?? 'Sin categoría' }}</p>
        <p><strong>Fecha del Evento:</strong> {{ \Carbon\Carbon::parse($reporte->fecha_evento)->format('d/m/Y H:i') }}</p>
        <p><strong>Lugar:</strong> {{ $reporte->lugar }}</p>
        <p><strong>Estado:</strong> <span class="badge bg-success">{{ ucfirst($reporte->estado) }}</span></p>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold">Detalles Adicionales</h6>
        @if($reporte->numero_personas)
            <p><strong>Número de Personas:</strong> {{ $reporte->numero_personas }}</p>
        @endif
        @if($reporte->latitud && $reporte->longitud)
            <p><strong>Coordenadas:</strong> {{ $reporte->latitud }}, {{ $reporte->longitud }}</p>
        @endif
        @if($reporte->fecha_aprobacion)
            <p><strong>Fecha de Aprobación:</strong> {{ \Carbon\Carbon::parse($reporte->fecha_aprobacion)->format('d/m/Y H:i') }}</p>
        @endif
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold">Descripción</h6>
        <div class="p-3 bg-light rounded">
            {{ $reporte->descripcion }}
        </div>
    </div>
</div>

@if($reporte->tema_tratado)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold">Tema Tratado</h6>
        <div class="p-3 bg-light rounded">
            {{ $reporte->tema_tratado }}
        </div>
    </div>
</div>
@endif

@if($reporte->acuerdos_compromisos)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold">Acuerdos y Compromisos</h6>
        <div class="p-3 bg-light rounded">
            {{ $reporte->acuerdos_compromisos }}
        </div>
    </div>
</div>
@endif

@if($reporte->presencia_autoridades)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold">Presencia de Autoridades</h6>
        <div class="p-3 bg-light rounded">
            {{ $reporte->presencia_autoridades }}
        </div>
    </div>
</div>
@endif

@if($reporte->intervencion_serenazgo)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold">Intervención de Serenazgo</h6>
        <div class="p-3 bg-light rounded">
            {{ $reporte->intervencion_serenazgo }}
        </div>
    </div>
</div>
@endif

@if($reporte->archivos && count($reporte->archivos) > 0)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold">Archivos Adjuntos</h6>
        <div class="row">
            @foreach($reporte->archivos as $archivo)
                @if($archivo->tipo == 'imagen')
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="{{ url('reportes/imagen/' . $archivo->url) }}" 
                                 class="card-img-top" 
                                 style="height: 150px; object-fit: cover; cursor: pointer;"
                                 onclick="window.open('{{ url('reportes/imagen/' . $archivo->url) }}', '_blank')"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjY2NjIi8+PHRleHQgeD0iNTAiIHk9IjUwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZW4gbm8gZW5jb250cmFkYTwvdGV4dD48L3N2Zz4=';">
                            <div class="card-body p-2">
                                <small class="text-muted">{{ $archivo->nombre_archivo ?? 'Imagen' }}</small>
                            </div>
                        </div>
                    </div>
                @elseif($archivo->tipo == 'enlace')
                    <div class="col-md-6 mb-2">
                        <div class="alert alert-info p-2">
                            <i class="fa fa-link"></i>
                            <a href="{{ $archivo->url }}" target="_blank" class="ms-2">{{ $archivo->url }}</a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Sección de Observación -->
<div class="row mt-4">
    <div class="col-12">
        <h6 class="fw-bold">Mi Observación</h6>
        @if(isset($respuestaExistente) && $respuestaExistente)
            <div class="alert alert-success">
                <strong>Ya respondido el:</strong> {{ \Carbon\Carbon::parse($respuestaExistente->fecha_respuesta)->format('d/m/Y H:i') }}
                <br>
                <strong>Observación:</strong> {{ $respuestaExistente->respuesta }}
            </div>
        @else
            <textarea id="observacionText" class="form-control" rows="4" 
                      placeholder="Escribe tu observación sobre este reporte..."></textarea>
        @endif
    </div>
</div>