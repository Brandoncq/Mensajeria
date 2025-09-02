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
        $reporte = Reporte::with('archivos')->findOrFail($id);
        $areas = AreaInteres::all();
        return view('dashboard.admin.reportes.show', compact('reporte', 'areas'));
    }

    // Editar
    public function edit($id)
    {
        $reporte = Reporte::with('archivos')->findOrFail($id);
        return view('dashboard.admin.reportes.edit', compact('reporte'));
    }

    public function update(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->update($request->all());

        return redirect()->route('admin.reportes.index')->with('success', 'Reporte actualizado correctamente');
    }

    // Aprobar
    public function aprobar(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);

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
        // Enviar notificación (ejemplo con Twilio)
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $from = env('TWILIO_FROM');
        $fromWhatsApp = env('TWILIO_WHATSAPP') ?? $from;
        
        $client = new \Twilio\Rest\Client($sid, $token);

        foreach ($usuarios as $detalle) {

            if ($detalle->usuario && $detalle->usuario->telefono) {
                // Asegurar formato con +51
                $to = $detalle->usuario->telefono;
                if (!str_starts_with($to, '+')) {
                    $to = '+51' . ltrim($to, '0'); // asume Perú, elimina 0 inicial
                }

                \Log::info("📱 Enviando SMS a: {$to}");

                $mensaje = "🚨 NUEVO REPORTE APROBADO\n";
                $mensaje .= "ID: #{$reporte->id_reporte}\n";
                $mensaje .= "Categoría: {$reporte->categoria->nombre}\n";
                $mensaje .= "Fecha: " . date('d/m/Y', strtotime($reporte->fecha_evento)) . "\n";
                $mensaje .= "Lugar: {$reporte->lugar}\n";
                $mensaje .= "Descripción: " . substr($reporte->descripcion, 0, 100) . "...\n";
                
                if ($reporte->numero_personas) {
                    $mensaje .= "Personas involucradas: {$reporte->numero_personas}\n";
                }
                
                if ($reporte->tema_tratado) {
                    $mensaje .= "Tema: " . substr($reporte->tema_tratado, 0, 50) . "...\n";
                }
                
                $mensaje .= "Estado: ✅ APROBADO\n";
                $mensaje .= "Ingresa a la plataforma: " . config('app.url');
                $client->messages->create($to, [
                    'from' => $from,
                    'body' => $mensaje,
                ]);
                if ($detalle->usuario->email) {
                    \Log::info("Entro");
                    Mail::to($detalle->usuario->email)
                        ->send(new ReporteAprobadoMail($reporte));
                }
                $client->messages->create("whatsapp:$to", [
                    'from' => "whatsapp:$fromWhatsApp",
                    "body" => $mensaje,
                ]);
            }
        }


        return redirect()->route('dashboard.administrador')
        ->with('success', 'Reporte aprobado y notificación enviada.');
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
