<?php
namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Archivo;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class ReporteController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Entrando al método store del ReporteController.');

            // Validar campos comunes
            $rules = [
                'id_categoria' => 'required|integer',
                'fecha_evento' => 'required|date',
                'lugar' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'actores_identificados' => 'nullable|array',
                'actores_roles' => 'nullable|array',
                'actores_vinculos' => 'nullable|array',
                'tema_tratado' => 'nullable|string',
                'acuerdos_compromisos' => 'nullable|string',
                'recomendacion_preliminar' => 'nullable|string',
            ];

            // Agregar validaciones específicas para la categoría 6
            if ($request->id_categoria == 6) {
                $rules = array_merge($rules, [
                    'latitud' => 'required|numeric',
                    'longitud' => 'required|numeric',
                    'numero_personas' => 'required|integer|min:1',
                    'presencia_autoridades' => 'required|string',
                    'intervencion_serenazgo' => 'required|string',
                ]);
            }

            // Validar los datos
            $validated = $request->validate($rules);

            // Procesar actores identificados
            $actoresTexto = '';
            if ($request->has('actores_identificados') && is_array($request->actores_identificados)) {
                $actores = [];
                $nombres = $request->actores_identificados ?? [];
                $roles = $request->actores_roles ?? [];
                $vinculos = $request->actores_vinculos ?? [];
                
                for ($i = 0; $i < count($nombres); $i++) {
                    if (!empty($nombres[$i])) {
                        $actor = trim($nombres[$i]);
                        if (!empty($roles[$i])) {
                            $actor .= ' (' . trim($roles[$i]) . ')';
                        }
                        if (!empty($vinculos[$i])) {
                            $actor .= ' - ' . trim($vinculos[$i]);
                        }
                        $actores[] = $actor;
                    }
                }
                $actoresTexto = implode('; ', $actores);
            }

            // Preparar datos para guardar
            $datosReporte = [
                'id_monitor' => Auth::id(),
                'id_categoria' => $validated['id_categoria'],
                'fecha_sistema' => now()->setTimezone('America/Lima'),
                'fecha_evento' => \Carbon\Carbon::parse($request->fecha_evento)->setTimezone('America/Lima'),
                'lugar' => $validated['lugar'],
                'descripcion' => $validated['descripcion'],
                'actores_identificados' => $actoresTexto,
                'tema_tratado' => $request->tema_tratado,
                'acuerdos_compromisos' => $request->acuerdos_compromisos,
                'recomendacion_preliminar' => $request->recomendacion_preliminar,
                'estado' => 'pendiente', // Estado inicial
            ];

            // Agregar campos específicos para categoría 6
            if ($request->id_categoria == 6) {
                $datosReporte['latitud'] = $validated['latitud'];
                $datosReporte['longitud'] = $validated['longitud'];
                $datosReporte['numero_personas'] = $validated['numero_personas'];
                $datosReporte['presencia_autoridades'] = $validated['presencia_autoridades'];
                $datosReporte['intervencion_serenazgo'] = $validated['intervencion_serenazgo'];
            }

            Log::info('Datos finales antes de guardar el reporte:', $datosReporte);

            $reporte = Reporte::create($datosReporte);

            // Guardar imágenes con nombres únicos
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    // Generar un nombre único para el archivo
                    $uniqueName = uniqid('img_', true) . '.' . $imagen->getClientOriginalExtension();

                    // Guardar el archivo en el almacenamiento
                    $path = $imagen->storeAs('reportes', $uniqueName, 'public');

                    // Guardar en la base de datos SOLO EL NOMBRE
                    Archivo::create([
                        'id_reporte' => $reporte->id_reporte,
                        'tipo' => 'imagen',
                        'url' => $uniqueName, // Solo el nombre del archivo
                        'nombre_archivo' => $uniqueName,
                    ]);

                    Log::info('Imagen guardada:', ['path' => $path, 'nombre' => $uniqueName]);
                }
            }

            // Guardar enlaces
            if ($request->filled('enlace')) {
                foreach ($request->enlace as $url) {
                    if (!empty(trim($url))) {
                        Archivo::create([
                            'id_reporte' => $reporte->id_reporte,
                            'tipo' => 'enlace',
                            'url' => trim($url),
                            'nombre_archivo' => null,
                        ]);

                        Log::info('Enlace guardado:', ['url' => $url]);
                    }
                }
            }

            $this->notificarAdministradores('Se ha enviado un nuevo reporte.');

            Log::info('Reporte enviado correctamente.');

            return redirect()->back()->with('success', 'Reporte enviado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al enviar el reporte:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Hubo un error al enviar el reporte. Inténtalo nuevamente.');
        }
    }

    public function notificarAdministradores($mensaje)
    {
        // Obtener credenciales de Twilio desde el archivo .env
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $from = env('TWILIO_FROM');
        $client = new Client($sid, $token);

        // Obtener el nombre del usuario autenticado
        $nombreUsuario = Auth::user()->nombre ?? Auth::user()->username ?? 'Usuario';

        // Crear el mensaje personalizado
        $mensajeSMS = "DECSAC MSS -> Reporte recibido de: $nombreUsuario";
        $mensajeWhatsApp = "🚨 *NUEVO REPORTE PENDIENTE*\n\n";
        $mensajeWhatsApp .= "Monitor: $nombreUsuario\n";
        $mensajeWhatsApp .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
        $mensajeWhatsApp .= "Estado: 📋 PENDIENTE REVISIÓN\n\n";
        $mensajeWhatsApp .= "Ingresa al sistema para revisar: " . config('app.url');

        // Obtener administradores con rol 'administrador'
        $admins = \App\Models\User::where('rol', 'administrador')->get();
        
        foreach ($admins as $admin) {
            if ($admin->telefono) {
                // Asegurar formato con +51
                $to = $admin->telefono;
                if (!str_starts_with($to, '+')) {
                    $to = '+51' . ltrim($to, '0');
                }

                try {
                    // Enviar SMS (como antes)
                    $client->messages->create($to, [
                        'from' => $from,
                        'body' => $mensajeSMS,
                    ]);

                    // Enviar WhatsApp
                    $client->messages->create("whatsapp:$to", [
                        'from' => "whatsapp:+14155238886", // Tu número de WhatsApp de Twilio
                        'body' => $mensajeWhatsApp,
                    ]);

                    Log::info("✅ SMS y WhatsApp enviados a administrador: {$to}");

                } catch (\Exception $e) {
                    Log::error("❌ Error enviando notificaciones a administrador {$to}: " . $e->getMessage());
                }
            }

            // Enviar Email si tiene correo
            if ($admin->email) {
                try {
                    Mail::to($admin->email)->send(new \App\Mail\NuevoReporteMail($nombreUsuario));
                    Log::info("✅ Email enviado a administrador: {$admin->email}");
                } catch (\Exception $e) {
                    Log::error("❌ Error enviando email a administrador {$admin->email}: " . $e->getMessage());
                }
            }
        }
    }
}