<?php
namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\AreaInteres;
use App\Models\DetalleInteres;
use App\Models\ReporteAsociado;
use App\Models\RespuestaAsociado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsociadoController extends Controller
{
    public function dashboard()
    {
        $areas = AreaInteres::all();
        $misAreas = DetalleInteres::where('id_usuario', Auth::id())
                                 ->pluck('id_area_interes')
                                 ->toArray();
        
        // Obtener reportes aprobados asignados al usuario
        $reportes = Reporte::where('estado', 'aprobado')
                          ->whereIn('id_reporte', function($query) {
                              $query->select('id_reporte')
                                    ->from('reporte_asociados')
                                    ->where('id_usuario', Auth::id());
                          })
                          ->with(['categoria', 'archivos'])
                          ->orderBy('fecha_sistema', 'desc')
                          ->get();

        // Agregar información de revisión a cada reporte
        foreach ($reportes as $reporte) {
            // Verificar si ya respondió en respuesta_asociados
            $respuesta = RespuestaAsociado::where('id_reporte', $reporte->id_reporte)
                                        ->where('id_usuario', Auth::id())
                                        ->first();
            
            $reporte->estado_revision = $respuesta ? 'revisado' : 'no_revisado';
            $reporte->respuesta_texto = $respuesta ? $respuesta->respuesta : null;
            
            // Fecha de asignación desde reporte_asociados
            $asignacion = ReporteAsociado::where('id_reporte', $reporte->id_reporte)
                                       ->where('id_usuario', Auth::id())
                                       ->first();
            
            $reporte->fecha_asignacion = $asignacion ? $asignacion->fecha_asignacion : now();
        }

        return view('dashboard.editor', compact('areas', 'misAreas', 'reportes'));
    }

    public function verReporte($id)
    {
        // Verificar que el reporte esté asignado al usuario y sea aprobado
        $reporte = Reporte::where('estado', 'aprobado')
                         ->whereIn('id_reporte', function($query) {
                             $query->select('id_reporte')
                                   ->from('reporte_asociados')
                                   ->where('id_usuario', Auth::id());
                         })
                         ->with(['categoria', 'archivos'])
                         ->findOrFail($id);
        
        // Obtener respuesta existente si la hay
        $respuestaExistente = RespuestaAsociado::where('id_reporte', $id)
                                              ->where('id_usuario', Auth::id())
                                              ->first();
        
        $html = view('partials.reporte-detalle-asociado', compact('reporte', 'respuestaExistente'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function marcarRevisado(Request $request, $id)
    {
        // Verificar que el reporte existe y está asignado al usuario
        $exists = ReporteAsociado::where('id_reporte', $id)
                                ->where('id_usuario', Auth::id())
                                ->exists();

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Reporte no encontrado o no asignado'
            ]);
        }

        // Crear o actualizar la respuesta
        $respuesta = RespuestaAsociado::updateOrCreate(
            [
                'id_reporte' => $id,
                'id_usuario' => Auth::id()
            ],
            [
                'respuesta' => $request->input('observacion', 'Revisado'),
                'fecha_respuesta' => now()
            ]
        );

        if ($respuesta) {
            return response()->json([
                'success' => true,
                'message' => 'Reporte marcado como revisado con observación'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al marcar como revisado'
        ]);
    }

    public function actualizarAreas(Request $request)
    {
        $userId = Auth::id();
        
        // Eliminar áreas actuales
        DetalleInteres::where('id_usuario', $userId)->delete();
        
        // Agregar nuevas áreas
        if ($request->has('areas')) {
            foreach ($request->areas as $areaId) {
                DetalleInteres::create([
                    'id_usuario' => $userId,
                    'id_area_interes' => $areaId
                ]);
            }
        }

        return redirect()->back()->with('success', 'Áreas de interés actualizadas correctamente');
    }
}