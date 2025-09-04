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
        $reporte = Reporte::with(['archivos', 'administradorAprobador'])->findOrFail($id);
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
        $reporte->estado = 'revisado';
        $reporte->fecha_aprobacion = now();
        $reporte->id_administrador_aprobador = auth()->id();
        $reporte->save();

        // Obtener el área seleccionada
        $idArea = $request->input('id_area_interes');
        \Log::info('🔎 Área seleccionada:', ['id_area_interes' => $idArea]);
        
        // Buscar usuarios interesados en esa área
        $usuarios = \App\Models\DetalleInteres::with('usuario')
            ->where('id_area_interes', $idArea)
            ->get();
        \Log::info('👥 Usuarios encontrados en área:', $usuarios->toArray());
        
        // Enviar notificación con Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $fromSms = env('TWILIO_FROM'); // Número para SMS
        $fromWhatsApp = env('TWILIO_WHATSAPP', 'whatsapp:+14155238886'); // Número para WhatsApp
        
        // Verificar que las credenciales de Twilio estén configuradas
        if (empty($sid) || empty($token)) {
            \Log::error('❌ Credenciales de Twilio no configuradas');
            return redirect()->route('dashboard.administrador')
                ->with('error', 'Configuración de Twilio incompleta. Notificaciones no enviadas.');
        }

        try {
            $client = new \Twilio\Rest\Client($sid, $token);

            foreach ($usuarios as $detalle) {
                if ($detalle->usuario && $detalle->usuario->telefono) {
                    // Asegurar formato con +51
                    $to = $detalle->usuario->telefono;
                    if (!str_starts_with($to, '+')) {
                        $to = '+51' . ltrim($to, '0'); // asume Perú, elimina 0 inicial
                    }

                    \Log::info("📱 Enviando notificaciones a: {$to}");

                    // Mensaje para SMS
                    $mensajeSMS = "🚨 NUEVO REPORTE APROBADO\n";
                    $mensajeSMS .= "ID: #{$reporte->id_reporte}\n";
                    $mensajeSMS .= "Categoría: {$reporte->categoria->nombre}\n";
                    $mensajeSMS .= "Fecha: " . date('d/m/Y', strtotime($reporte->fecha_evento)) . "\n";
                    $mensajeSMS .= "Lugar: {$reporte->lugar}\n";
                    $mensajeSMS .= "Descripción: " . substr($reporte->descripcion, 0, 100) . "...\n";
                    
                    if ($reporte->numero_personas) {
                        $mensajeSMS .= "Personas involucradas: {$reporte->numero_personas}\n";
                    }
                    
                    if ($reporte->tema_tratado) {
                        $mensajeSMS .= "Tema: " . substr($reporte->tema_tratado, 0, 50) . "...\n";
                    }
                    
                    $mensajeSMS .= "Estado: ✅ APROBADO\n";
                    $mensajeSMS .= "Ingresa a la plataforma: " . config('app.url');

                    // Mensaje para WhatsApp (más detallado y con formato)
                    $mensajeWhatsApp = "🚨 *NUEVO REPORTE APROBADO*\n\n";
                    $mensajeWhatsApp .= "📋 *ID:* #{$reporte->id_reporte}\n";
                    $mensajeWhatsApp .= "📁 *Categoría:* {$reporte->categoria->nombre}\n";
                    $mensajeWhatsApp .= "📅 *Fecha:* " . date('d/m/Y', strtotime($reporte->fecha_evento)) . "\n";
                    $mensajeWhatsApp .= "📍 *Lugar:* {$reporte->lugar}\n";
                    $mensajeWhatsApp .= "📝 *Descripción:* " . substr($reporte->descripcion, 0, 150) . "...\n";
                    
                    if ($reporte->numero_personas) {
                        $mensajeWhatsApp .= "👥 *Personas involucradas:* {$reporte->numero_personas}\n";
                    }
                    
                    if ($reporte->tema_tratado) {
                        $mensajeWhatsApp .= "💬 *Tema:* " . substr($reporte->tema_tratado, 0, 80) . "...\n";
                    }
                    
                    $mensajeWhatsApp .= "✅ *Estado:* APROBADO\n\n";
                    $mensajeWhatsApp .= "🌐 *Ingresa a la plataforma:* " . config('app.url');

                    try {
                        // Enviar SMS (From y To son números regulares)
                        $client->messages->create($to, [
                            'from' => $fromSms, // Número regular de Twilio
                            'body' => $mensajeSMS,
                        ]);
                        \Log::info("✅ SMS enviado correctamente a: {$to}");

                    } catch (\Exception $e) {
                        \Log::error("❌ Error enviando SMS a {$to}: " . $e->getMessage());
                    }

                    try {
                        // Enviar WhatsApp (From y To deben ser números de WhatsApp)
                        $client->messages->create(
                            "whatsapp:{$to}", // Número destino en formato WhatsApp
                            [
                                'from' => $fromWhatsApp, // Número origen en formato WhatsApp
                                'body' => $mensajeWhatsApp
                            ]
                        );
                        \Log::info("✅ WhatsApp enviado correctamente a: {$to}");

                    } catch (\Exception $e) {
                        \Log::error("❌ Error enviando WhatsApp a {$to}: " . $e->getMessage());
                    }

                    // Enviar correo electrónico si tiene email
                    if ($detalle->usuario->email) {
                        try {
                            Mail::to($detalle->usuario->email)
                                ->send(new ReporteAprobadoMail($reporte));
                            \Log::info("✅ Email enviado a: {$detalle->usuario->email}");
                        } catch (\Exception $e) {
                            \Log::error("❌ Error enviando email a {$detalle->usuario->email}: " . $e->getMessage());
                        }
                    }

                    // Registrar notificación en base de datos
                    \App\Models\Notificacion::create([
                        'id_reporte' => $id,
                        'id_usuario_destino' => $detalle->usuario->id_usuario,
                        'tipo' => 'sms_whatsapp',
                        'contenido' => $mensajeSMS,
                        'fecha_envio' => now(),
                        'estado' => 'enviado',
                        'intentos' => 1,
                        'error_mensaje' => null
                    ]);
                }
            }

            return redirect()->route('dashboard.administrador')
                ->with('success', 'Reporte aprobado y notificaciones enviadas.');

        } catch (\Exception $e) {
            \Log::error('❌ Error general en el proceso de aprobación: ' . $e->getMessage());
            
            return redirect()->route('dashboard.administrador')
                ->with('error', 'Reporte aprobado, pero hubo problemas con las notificaciones: ' . $e->getMessage());
        }
    }
    // Rechazar
    public function rechazar($id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->estado = 'rechazado';
        $reporte->id_administrador_aprobador = auth()->id();
        $reporte->save();

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
