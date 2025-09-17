<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Para imprimir en PDF
use App\Models\DetalleInteres;
use App\Models\AreaInteres;
use Twilio\Rest\Client;
use App\Mail\ReporteAprobadoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage; // Añadir esta línea
use Illuminate\Support\Facades\DB; // También es buena práctica importar DB

class ReporteAdminController extends Controller
{
    // Listar reportes
    public function index()
    {
        $usuarios = User::orderBy('nombre', 'asc')->get();
        $reportes = Reporte::orderBy('fecha_sistema','desc')->get();
        return view('dashboard.administrador', compact('usuarios', 'reportes'));
    }

    // Ver detalle
    public function show($id)
    {
        $reporte = Reporte::with(['archivos', 'administradorAprobador', 'respuestasAsociados'])->findOrFail($id);
        $areas = AreaInteres::all();
        return view('dashboard.admin.reportes.show', compact('reporte', 'areas'));
    }

    // Editar
    public function edit($id)
    {
        $reporte = Reporte::with('archivos')->findOrFail($id);
        return view('dashboard.admin.reportes.edit', compact('reporte'));
    }

    // En el método update del controlador
    public function update(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);
        
        \DB::beginTransaction();
        try {
            // Preparar datos para actualizar
            $datosActualizar = $request->except(['archivos_eliminar', 'nuevas_imagenes', 'nuevos_enlaces']);
            
            // Actualizar el id_administrador_aprobador con el usuario actual
            $datosActualizar['id_administrador_aprobador'] = auth()->id();
            
            // Actualizar datos del reporte
            $reporte->update($datosActualizar);
            
            // Eliminar archivos marcados para eliminación
            if ($request->has('archivos_eliminar') && !empty($request->archivos_eliminar)) {
                $archivosEliminar = explode(',', $request->archivos_eliminar);
                
                foreach ($archivosEliminar as $archivoId) {
                    $archivo = \App\Models\Archivo::find($archivoId);
                    if ($archivo) {
                        // Eliminar archivo físico si es una imagen
                        if ($archivo->tipo === 'imagen' && Storage::exists('archivos/' . $archivo->nombre_archivo)) {
                            Storage::delete('archivos/' . $archivo->nombre_archivo);
                        }
                        $archivo->delete();
                    }
                }
            }
            
            // Añadir nuevas imágenes
            if ($request->hasFile('nuevas_imagenes')) {
                foreach ($request->file('nuevas_imagenes') as $imagen) {
                    if ($imagen->isValid()) {
                        $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
                        $imagen->storeAs('archivos', $nombreArchivo);
                        
                        \App\Models\Archivo::create([
                            'id_reporte' => $reporte->id_reporte,
                            'nombre_archivo' => $nombreArchivo,
                            'tipo' => 'imagen'
                        ]);
                    }
                }
            }
            
            // Añadir nuevos enlaces
            if ($request->has('nuevos_enlaces')) {
                foreach ($request->nuevos_enlaces as $enlace) {
                    if (!empty($enlace)) {
                        \App\Models\Archivo::create([
                            'id_reporte' => $reporte->id_reporte,
                            'url' => $enlace,
                            'tipo' => 'enlace'
                        ]);
                    }
                }
            }
            
            \DB::commit();
            
            return redirect()->route('admin.reportes.index')->with('success', 'Reporte actualizado correctamente');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al actualizar reporte: ' . $e->getMessage());
            
            return back()->with('error', 'Error al actualizar el reporte: ' . $e->getMessage())->withInput();
        }
    }

    // Aprobar¿
    public function aprobar(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);
        \App\Models\Notificacion::where('id_reporte', $id)->delete();
        
        // Actualizar estado
        $reporte->estado = 'aprobado';
        $reporte->fecha_aprobacion = now();
        $reporte->id_administrador_aprobador = auth()->id();
        $reporte->save();

        // Obtener el área seleccionada
        $idArea = $request->input('id_area_interes');
        \Log::info('🔎 Área seleccionada:', ['id_area_interes' => $idArea]);
        
        // Buscar usuarios interesados
        if ($idArea == 'todos') {
            // ✅ Obtener TODOS los usuarios (excepto administradores si quieres)
            $usuarios = \App\Models\User::whereNotIn('rol', ['administrador', 'monitor'])->get();
            \Log::info('👥 Todos los usuarios seleccionados:', $usuarios->toArray());
        } else {
            // Buscar usuarios por área específica
            $usuarios = \App\Models\DetalleInteres::with('usuario')
                ->where('id_area_interes', $idArea)
                ->get()
                ->pluck('usuario')
                ->filter(); // Eliminar nulls
            \Log::info('👥 Usuarios encontrados en área:', $usuarios->toArray());
        }
        foreach ($usuarios as $usuario) {
            $user = ($usuario instanceof \App\Models\DetalleInteres) ? $usuario->usuario : $usuario;
            if ($user) {
                // Verificar si ya existe esta asignación
                $asignacionExistente = \App\Models\ReporteAsociado::where('id_reporte', $reporte->id_reporte)
                    ->where('id_usuario', $user->id_usuario)
                    ->first();
                
                if (!$asignacionExistente) {
                    \App\Models\ReporteAsociado::create([
                        'id_reporte' => $reporte->id_reporte,
                        'id_usuario' => $user->id_usuario,
                        'fecha_asignacion' => now()
                    ]);
                    \Log::info("✅ Usuario asociado al reporte: {$user->id_usuario}");
                } else {
                    \Log::info("ℹ️ Usuario ya estaba asociado al reporte: {$user->id_usuario}");
                }
            }
        }
        // Enviar notificación con Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $fromSms = env('TWILIO_FROM');
        
        // Verificar credenciales
        if (empty($sid) || empty($token) || empty($fromSms)) {
            \Log::error('❌ Credenciales de Twilio no configuradas');
            return redirect()->route('dashboard.administrador')
                ->with('error', 'Configuración de Twilio incompleta. Notificaciones no enviadas.');
        }

        try {
            $client = new \Twilio\Rest\Client($sid, $token);
            $smsEnviados = 0;
            $erroresSms = 0;

            foreach ($usuarios as $usuario) {
                // ✅ Manejar tanto colección de DetalleInteres como de User directamente
                $user = ($usuario instanceof \App\Models\DetalleInteres) ? $usuario->usuario : $usuario;
                
                if ($user && $user->telefono) {
                    // Formatear número
                    $to = $user->telefono;
                    if (!str_starts_with($to, '+')) {
                        $to = '+51' . ltrim($to, '0');
                    }

                    \Log::info("📱 Intentando enviar SMS a: {$to}");

                    $mensajeSMS = "Reporte #{$reporte->id_reporte} revisado. Ver: " . config('app.url');

                    try {
                        // Enviar SMS
                        $message = $client->messages->create($to, [
                            'from' => $fromSms,
                            'body' => $mensajeSMS,
                        ]);
                        
                        \Log::info("✅ SMS enviado correctamente a: {$to}");
                        $smsEnviados++;

                        // Registrar notificación
                        \App\Models\Notificacion::create([
                            'id_reporte' => $id,
                            'id_usuario_destino' => $user->id_usuario,
                            'tipo' => 'sms',
                            'contenido' => $mensajeSMS,
                            'fecha_envio' => now(),
                            'estado' => 'enviado',
                            'intentos' => 1,
                            'error_mensaje' => null
                        ]);

                    } catch (\Exception $e) {
                        \Log::error("❌ Error enviando SMS a {$to}: " . $e->getMessage());
                        $erroresSms++;

                        \App\Models\Notificacion::create([
                            'id_reporte' => $id,
                            'id_usuario_destino' => $user->id_usuario,
                            'tipo' => 'sms',
                            'contenido' => $mensajeSMS,
                            'fecha_envio' => now(),
                            'estado' => 'error',
                            'intentos' => 1,
                            'error_mensaje' => $e->getMessage()
                        ]);
                    }

                    // Enviar email si tiene
                    if ($user->email) {
                        try {
                            Mail::to($user->email)
                                ->send(new ReporteAprobadoMail($reporte));
                            \Log::info("✅ Email enviado a: {$user->email}");
                        } catch (\Exception $e) {
                            \Log::error("❌ Error enviando email a {$user->email}: " . $e->getMessage());
                        }
                    }
                }
            }

            \Log::info("📊 Resumen: {$smsEnviados} SMS enviados, {$erroresSms} errores");

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Reporte aprobado. {$smsEnviados} SMS enviados, {$erroresSms} errores."
                ]);
            }
            return redirect()->route('dashboard.administrador')
            ->with('success', "Reporte aprobado. {$smsEnviados} SMS enviados, {$erroresSms} errores.");

        } catch (\Exception $e) {
            \Log::error('❌ Error general: ' . $e->getMessage());
             if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aprobar el reporte: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('dashboard.administrador')
                ->with('error', 'Reporte aprobado, pero hubo problemas: ' . $e->getMessage());
        }
    }
    // Rechazar
    public function rechazar(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->estado = 'rechazado';
        $reporte->id_administrador_aprobador = auth()->id();
        $reporte->save();
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reporte rechazado correctamente'
            ]);
        }
        return back()->with('error', 'Reporte rechazado');
    }

    // Eliminar
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            // Encontrar el reporte
            $reporte = Reporte::findOrFail($id);

            // Eliminar registros relacionados en orden adecuado
            \App\Models\Archivo::where('id_reporte', $id)->delete();
            \App\Models\Notificacion::where('id_reporte', $id)->delete();
            \App\Models\ReporteAsociado::where('id_reporte', $id)->delete();
            \App\Models\EdicionReporte::where('id_reporte', $id)->delete();
            \App\Models\RespuestaAsociado::where('id_reporte', $id)->delete();
            
            // Finalmente eliminar el reporte
            $reporte->delete();

            \DB::commit();

            return redirect()->route('admin.reportes.index')
                ->with('success', 'Reporte y todos sus datos relacionados eliminados correctamente');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al eliminar reporte: ' . $e->getMessage());
            
            return redirect()->route('admin.reportes.index')
                ->with('error', 'Error al eliminar el reporte: ' . $e->getMessage());
        }
    }

    // Imprimir en PDF
    public function imprimir($id)
    {
        $reporte = Reporte::with('archivos')->findOrFail($id);

        $pdf = Pdf::loadView('dashboard.admin.reportes.pdf', compact('reporte'));
        return $pdf->download("reporte_{$reporte->id_reporte}.pdf");
    }
}
