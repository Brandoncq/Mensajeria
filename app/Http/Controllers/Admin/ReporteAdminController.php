<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Para imprimir en PDF
use App\Models\DetalleInteres;
use App\Models\AreaInteres;
use Twilio\Rest\Client;

class ReporteAdminController extends Controller
{
    // Listar reportes
    public function index()
    {
      $reportes = Reporte::with('categoria')
        ->orderBy('fecha_sistema', 'desc')
        ->get();

    return view('dashboard.admin.reportes.index', compact('reportes'));
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

        return redirect()->route('dashboard.admin.reportes.index')->with('success', 'Reporte actualizado correctamente');
    }

    // Aprobar
    public function aprobar(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);

        // Actualizar estado
        $reporte->estado = 'aprobado';
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
        $client = new \Twilio\Rest\Client($sid, $token);

        foreach ($usuarios as $detalle) {

            if ($detalle->usuario && $detalle->usuario->telefono) {
                // Asegurar formato con +51
                $to = $detalle->usuario->telefono;
                if (!str_starts_with($to, '+')) {
                    $to = '+51' . ltrim($to, '0'); // asume Perú, elimina 0 inicial
                }

                \Log::info("📱 Enviando SMS a: {$to}");

                $mensaje = "Se aprobó el reporte #{$reporte->id_reporte}.";
                $client->messages->create($to, [
                    'from' => $from,
                    'body' => $mensaje,
                ]);
            }
        }


        return back()->with('success', 'Reporte aprobado y notificación enviada.');
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
        Reporte::destroy($id);
        return redirect()->route('dashboard.admin.reportes.index')->with('success', 'Reporte eliminado');
    }

    // Imprimir en PDF
    public function imprimir($id)
    {
        $reporte = Reporte::with('archivos')->findOrFail($id);

        $pdf = Pdf::loadView('dashboard.admin.reportes.pdf', compact('reporte'));
        return $pdf->download("reporte_{$reporte->id_reporte}.pdf");
    }
}
