<?php
namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Archivo;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Asegúrate de importar la clase Log

class ReporteController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Log para verificar que el método se ejecuta
            Log::info('Entrando al método store del ReporteController.');

            // Validar campos comunes
            $rules = [
                'id_categoria' => 'required|integer',
                'fecha_evento' => 'required|date',
                'lugar' => 'required|string|max:255',
                'descripcion' => 'required|string',
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

            // Log para verificar los datos validados
            Log::info('Datos validados:', $validated);

            $validated['id_monitor'] = Auth::id(); // Asigna el monitor autenticado
            $validated['fecha_sistema'] = now()->setTimezone('America/Lima'); // Fecha del sistema en hora de Perú
            $validated['fecha_evento'] = \Carbon\Carbon::parse($request->fecha_evento)->setTimezone('America/Lima'); // Fecha del evento en hora de Perú

            // Log para verificar los datos antes de guardar
            Log::info('Datos finales antes de guardar el reporte:', $validated);

            $reporte = Reporte::create($validated);

            // Guardar imágenes con nombres únicos
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    // Generar un nombre único para el archivo
                    $uniqueName = uniqid('img_', true) . '.' . $imagen->getClientOriginalExtension();

                    // Guardar el archivo en el almacenamiento
                    $path = $imagen->storeAs('reportes', $uniqueName, 'public');

                    // Guardar en la base de datos
                    Archivo::create([
                        'id_reporte' => $reporte->id_reporte,
                        'tipo' => 'imagen',
                        'url' => $path,
                        'nombre_archivo' => $uniqueName, // Guardar el nombre único generado
                    ]);

                    // Log para verificar cada imagen guardada
                    Log::info('Imagen guardada:', ['path' => $path, 'nombre' => $uniqueName]);
                }
            }

            // Guardar enlace
            if ($request->filled('enlace')) {
                foreach ($request->enlace as $url) {
                    Archivo::create([
                        'id_reporte' => $reporte->id_reporte,
                        'tipo' => 'enlace',
                        'url' => $url,
                        'nombre_archivo' => null,
                    ]);

                    // Log para verificar cada enlace guardado
                    Log::info('Enlace guardado:', ['url' => $url]);
                }
            }

            $this->notificarAdministradores('Se ha enviado un nuevo reporte.');

            // Log para confirmar que todo se ejecutó correctamente
            Log::info('Reporte enviado correctamente.');

            // Redirigir con mensaje de éxito
            return redirect()->back()->with('success', 'Reporte enviado correctamente.');
        } catch (\Exception $e) {
            // Log para capturar errores
            Log::error('Error al enviar el reporte:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Redirigir con mensaje de error
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
        $nombreUsuario = Auth::user()->nombre; // Asegúrate de que el modelo User tenga el campo 'nombre'

        // Crear el mensaje personalizado
        $mensaje = "DECSAC MSS -> Reporte recibido de: $nombreUsuario";

        // Obtener administradores con rol 'administrador'
        $admins = \App\Models\User::where('rol', 'administrador')->get();
        foreach ($admins as $admin) {
            if ($admin->telefono) {
                $client->messages->create($admin->telefono, [
                    'from' => $from,
                    'body' => $mensaje,
                ]);
            }
        }
    }
}