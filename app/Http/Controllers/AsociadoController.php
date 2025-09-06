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
use Illuminate\Support\Facades\Log;

class AsociadoController extends Controller
{
    public function dashboard()
    {
        $areas = AreaInteres::all();
        $misAreas = DetalleInteres::where('id_usuario', Auth::id())
                                 ->pluck('id_area_interes')
                                 ->toArray();
        
        // Obtener reportes aprobados Y revisados asignados al usuario
        $reportes = Reporte::whereIn('estado', ['aprobado', 'revisado'])
                          ->whereIn('id_reporte', function($query) {
                              $query->select('id_reporte')
                                    ->from('reporte_asociados')
                                    ->where('id_usuario', Auth::id());
                          })
                          ->with(['categoria', 'archivos'])
                          ->orderBy('fecha_sistema', 'desc')
                          ->get();

        // Ya no necesitamos agregar respuesta_texto porque usaremos el estado directamente
        foreach ($reportes as $reporte) {
            $asignacion = ReporteAsociado::where('id_reporte', $reporte->id_reporte)
                                       ->where('id_usuario', Auth::id())
                                       ->first();
            
            $reporte->fecha_asignacion = $asignacion ? $asignacion->fecha_asignacion : now();
        }

        return view('dashboard.editor', compact('areas', 'misAreas', 'reportes'));
    }

    public function verReporte($id)
    {
        // Verificar que el reporte esté asignado al usuario y sea aprobado O revisado
        $reporte = Reporte::whereIn('estado', ['aprobado', 'revisado'])
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
        try {
            DB::beginTransaction();

            // Verificar que el reporte existe y está asignado al usuario
            $reporte = Reporte::whereIn('estado', ['aprobado', 'revisado'])
                             ->whereIn('id_reporte', function($query) {
                                 $query->select('id_reporte')
                                      ->from('reporte_asociados')
                                      ->where('id_usuario', Auth::id());
                             })
                             ->findOrFail($id);

            // Solo permitir marcar como revisado si está en estado aprobado
            if ($reporte->estado !== 'aprobado') {
                throw new \Exception('El reporte ya fue revisado anteriormente.');
            }

            // Crear la respuesta
            RespuestaAsociado::create([
                'id_reporte' => $id,
                'id_usuario' => Auth::id(),
                'respuesta' => $request->input('observacion'),
                'fecha_respuesta' => now()
            ]);

            // Actualizar estado del reporte a 'revisado'
            $reporte->estado = 'revisado';
            $reporte->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reporte marcado como revisado'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al marcar reporte como revisado: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como revisado: ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarAreas(Request $request)
    {
        $userId = Auth::id();
        
        // Debug: Ver qué datos llegan
        Log::info('Datos recibidos para áreas:', [
            'user_id' => $userId,
            'areas' => $request->all(),
            'areas_array' => $request->areas ?? []
        ]);
        
        try {
            // Eliminar todas las áreas actuales del usuario
            $deleted = DetalleInteres::where('id_usuario', $userId)->delete();
            Log::info('Áreas eliminadas:', ['deleted_count' => $deleted]);
            
            // Agregar nuevas áreas seleccionadas
            if ($request->has('areas') && is_array($request->areas)) {
                foreach ($request->areas as $areaId) {
                    // Verificar que el área existe
                    if (AreaInteres::where('id_area_interes', $areaId)->exists()) {
                        $created = DetalleInteres::create([
                            'id_usuario' => $userId,
                            'id_area_interes' => $areaId
                        ]);
                        Log::info('Área creada:', ['area_id' => $areaId, 'created' => $created ? 'yes' : 'no']);
                    }
                }
            } else {
                Log::info('No hay áreas para agregar');
            }

            return redirect()->back()->with('success', 'Áreas de interés actualizadas correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar áreas:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al actualizar áreas de interés: ' . $e->getMessage());
        }
    }
}