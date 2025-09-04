<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Aprobado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #e74c3c;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .footer {
            background-color: #34495e;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
        }
        .btn {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 Nuevo Reporte Aprobado</h1>
        <p>Sistema de Monitoreo Social - DECSAC</p>
    </div>

    <div class="content">
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

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ config('app.url') }}" target="_blank" class="btn">Ver Reporte</a>
        </div>

        <p>
            <a href="{{ config('app.url') }}" target="_blank">
                {{ config('app.url') }}
            </a>
        </p>
    </div>

    <div class="footer">
        <p><strong>DECSAC - Sistema de Monitoreo Social</strong></p>
        <p>Este es un mensaje automático, no responder a este correo.</p>
        <p><small>{{ now()->format('d/m/Y H:i') }}</small></p>
    </div>
</body>
</html>
