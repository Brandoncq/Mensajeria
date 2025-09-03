<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Reporte</title>
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
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
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
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 NUEVO REPORTE PENDIENTE</h1>
        <p>Sistema de Monitoreo Social - DECSAC</p>
    </div>

    <div class="content">
        <div class="alert">
            ⚠️ Se ha recibido un nuevo reporte que requiere tu revisión.
        </div>

        <div class="info">
            <strong>Monitor:</strong> {{ $nombreMonitor }}<br>
            <strong>Fecha de envío:</strong> {{ now()->format('d/m/Y H:i') }}<br>
            <strong>Estado:</strong> 📋 Pendiente de revisión
        </div>

        <p>Un monitor social ha enviado un nuevo reporte al sistema. Como administrador, debes revisarlo y decidir si aprobarlo o rechazarlo.</p>

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ config('app.url') }}" class="btn">Revisar Reporte</a>
        </div>

        <p><small><strong>Nota:</strong> Es importante revisar los reportes lo antes posible para mantener actualizado el sistema de monitoreo.</small></p>
    </div>

    <div class="footer">
        <p><strong>DECSAC - Sistema de Monitoreo Social</strong></p>
        <p>Este es un mensaje automático, no responder a este correo.</p>
        <p><small>{{ now()->format('d/m/Y H:i') }}</small></p>
    </div>
</body>
</html>