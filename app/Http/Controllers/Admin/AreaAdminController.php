<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaInteres;
use App\Models\DetalleInteres;
use Illuminate\Http\Request;

class AreaAdminController extends Controller
{
    public function index()
    {
        $areas = AreaInteres::orderBy('nombre', 'asc')->get();

        return response()->json($areas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:areainteres,nombre',
        ]);

        $area = AreaInteres::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Área creada correctamente',
            'area' => $area,
        ]);
    }

    public function show($id)
    {
        $area = AreaInteres::findOrFail($id);

        return response()->json($area);
    }

    public function update(Request $request, $id)
    {
        $area = AreaInteres::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:areainteres,nombre,'.$id.',id_area_interes',
        ]);

        $area->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Área actualizada correctamente',
            'area' => $area,
        ]);
    }

    public function destroy($id)
    {
        $area = AreaInteres::findOrFail($id);

        // Eliminar en cascada los detalles relacionados
        DetalleInteres::where('id_area_interes', $id)->delete();

        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Área eliminada correctamente',
        ]);
    }

    public function getUsuariosPorArea($id)
    {
        try {
            \Log::info('🔍 Iniciando getUsuariosPorArea', ['id_solicitado' => $id]);

            if ($id == 'todos') {
                \Log::info('📋 Obteniendo TODOS los usuarios (excepto admin/monitor)');
                $usuarios = User::whereNotIn('rol', ['administrador', 'monitor'])->get();
                \Log::info('✅ Usuarios encontrados (todos)', ['count' => $usuarios->count()]);
            } else {
                \Log::info('📋 Buscando usuarios para área específica', ['id_area' => $id]);

                $detallesInteres = DetalleInteres::with(['usuario' => function ($query) {
                    $query->whereNotIn('rol', ['administrador', 'monitor']);
                }])
                    ->where('id_area_interes', $id)
                    ->get();

                \Log::info('📊 DetallesInteres encontrados', [
                    'count' => $detallesInteres->count(),
                    'detalles_ids' => $detallesInteres->pluck('id')->toArray(),
                ]);

                $usuarios = $detallesInteres->pluck('usuario')
                    ->filter()
                    ->values();

                \Log::info('👥 Usuarios filtrados', [
                    'count' => $usuarios->count(),
                    'usuarios_ids' => $usuarios->pluck('id_usuario')->toArray(),
                    'usuarios_nombres' => $usuarios->pluck('nombre')->toArray(),
                ]);
            }

            \Log::info('📦 Preparando respuesta JSON', ['total_usuarios' => $usuarios->count()]);

            return response()->json([
                'success' => true,
                'usuarios' => $usuarios,
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error en getUsuariosPorArea', [
                'id_solicitado' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar usuarios: '.$e->getMessage(),
            ], 500);
        }
    }
}
