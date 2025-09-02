<h1>🚨 Nuevo Reporte Aprobado</h1>
<p><strong>ID:</strong> #{{ $reporte->id_reporte }}</p>
<p><strong>Categoría:</strong> {{ $reporte->categoria->nombre }}</p>
<p><strong>Fecha:</strong> {{ date('d/m/Y', strtotime($reporte->fecha_evento)) }}</p>
<p><strong>Lugar:</strong> {{ $reporte->lugar }}</p>
<p><strong>Descripción:</strong> {{ $reporte->descripcion }}</p>
@if($reporte->numero_personas)
    <p><strong>Personas involucradas:</strong> {{ $reporte->numero_personas }}</p>
@endif
@if($reporte->tema_tratado)
    <p><strong>Tema:</strong> {{ $reporte->tema_tratado }}</p>
@endif
<p><strong>Estado:</strong> ✅ Aprobado</p>
<p>Ingresa a la plataforma para más detalles.</p>
<p>
    <a href="{{ config('app.url') }}" target="_blank">
        {{ config('app.url') }}
    </a>
</p>
