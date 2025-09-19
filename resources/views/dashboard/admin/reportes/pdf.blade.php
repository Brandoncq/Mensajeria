<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte #{{ $reporte->id_reporte }}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      margin: 20px;
    }
    h1 {
      text-align: center;
      color: #c1392b;
      font-size: 20px;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    th {
      background: #f2f2f2;
      text-align: left;
      font-weight: bold;
    }
    td, th {
      border: 1px solid #000;
      padding: 6px 8px;
    }
    .estado {
      font-weight: bold;
      text-transform: capitalize;
    }
    .aprobado { color: green; }
    .rechazado { color: red; }
    .pendiente { color: orange; }
    .borrador { color: gray; }
    h3 { margin-top: 20px; }
    ul { list-style: none; padding-left: 0; }
    li { margin-bottom: 10px; }
  </style>
</head>
<body>
  <h1>Reporte #{{ $reporte->id_reporte }}</h1>

  <table>
    <tr>
      <th>Categoría</th>
      <td>{{ $reporte->categoria->nombre ?? 'Sin categoría' }}</td>
    </tr>
    <tr>
      <th>Lugar</th>
      <td>{{ $reporte->lugar }}</td>
    </tr>
    <tr>
      <th>Descripción</th>
      <td>{{ $reporte->descripcion }}</td>
    </tr>
    @if(!empty($reporte->numero_personas))
    <tr>
      <th>Número de Personas</th>
      <td>{{ $reporte->numero_personas }}</td>
    </tr>
    @endif
    @if(!empty($reporte->actores_identificados))
    <tr>
      <th>Actores Identificados</th>
      <td>{{ $reporte->actores_identificados }}</td>
    </tr>
    @endif
    @if(!empty($reporte->presencia_autoridades))
    <tr>
      <th>Presencia de Autoridades</th>
      <td>{{ $reporte->presencia_autoridades }}</td>
    </tr>
    @endif
    @if(!empty($reporte->intervencion_serenazgo))
    <tr>
      <th>Intervención Serenazgo</th>
      <td>{{ $reporte->intervencion_serenazgo }}</td>
    </tr>
    @endif
    @if(!empty($reporte->tema_tratado))
    <tr>
      <th>Tema Tratado</th>
      <td>{{ $reporte->tema_tratado }}</td>
    </tr>
    @endif
    @if(!empty($reporte->acuerdos_compromisos))
    <tr>
      <th>Acuerdos y Compromisos</th>
      <td>{{ $reporte->acuerdos_compromisos }}</td>
    </tr>
    @endif
    @if(!empty($reporte->recomendacion_preliminar))
    <tr>
      <th>Recomendación Preliminar</th>
      <td>{{ $reporte->recomendacion_preliminar }}</td>
    </tr>
    @endif
    <tr>
      <th>Estado</th>
      <td class="estado 
        @if($reporte->estado == 'aprobado') aprobado 
        @elseif($reporte->estado == 'rechazado') rechazado 
        @elseif($reporte->estado == 'pendiente') pendiente 
        @else borrador @endif">
        {{ ucfirst($reporte->estado) }}
      </td>
    </tr>
    @if(!empty($reporte->fecha_evento))
    <tr>
      <th>Fecha Evento</th>
      <td>{{ $reporte->fecha_evento }}</td>
    </tr>
    @endif
    @if(!empty($reporte->fecha_aprobacion))
    <tr>
      <th>Fecha Aprobación</th>
      <td>{{ $reporte->fecha_aprobacion }}</td>
    </tr>
    @endif
    @if(!empty($reporte->latitud) || !empty($reporte->longitud))
    <tr>
      <th>Latitud / Longitud</th>
      <td>{{ $reporte->latitud }} , {{ $reporte->longitud }}</td>
    </tr>
    @endif
    @if(!empty($reporte->id_administrador_aprobador))
    <tr>
      <th>Administrador Aprobador</th>
      <td>{{ $reporte->administrador_aprobador->nombre ?? $reporte->id_administrador_aprobador }}</td>
    </tr>
    @endif
  </table>

  <h3>Archivos Adjuntos</h3>
  <ul>
    @forelse($reporte->archivos as $archivo)
        @if($archivo->tipo === 'imagen')
            <li>
                <div class="archivo-info">Imagen: {{ $archivo->nombre_archivo }}</div>
                <div>URL: {{ url('reportes/imagen/' . $archivo->nombre_archivo) }}</div>
            </li>
        @elseif($archivo->tipo === 'enlace')
            <li>
                <div class="archivo-info">Enlace:</div>
                <div>{{ $archivo->url }}</div>
            </li>
        @endif
    @empty
        <li>No hay archivos adjuntos.</li>
    @endforelse
  </ul>

  <p><small>Generado el {{ now()->format('d/m/Y H:i') }}</small></p>
</body>
</html>
