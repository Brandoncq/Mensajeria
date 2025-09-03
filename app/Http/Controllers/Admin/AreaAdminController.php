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
            'area' => $area
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
            'nombre' => 'required|string|max:255|unique:areainteres,nombre,' . $id . ',id_area_interes',
        ]);

        $area->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Área actualizada correctamente',
            'area' => $area
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
            'message' => 'Área eliminada correctamente'
        ]);
    }
}