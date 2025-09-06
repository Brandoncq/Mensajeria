<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Http\Response;
use Dompdf\Dompdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportWord($id)
    {
        $reporte = Reporte::with(['categoria', 'archivos', 'administradorAprobador', 'respuestasAsociados'])
                          ->findOrFail($id);
        
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        // Título
        $section->addText('REPORTE DETALLADO #' . $reporte->id_reporte, ['bold' => true, 'size' => 16]);
        $section->addTextBreak(1);
        
        // Información General
        $section->addText('INFORMACIÓN GENERAL', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
        $section->addText('ID Reporte: ' . $reporte->id_reporte);
        $section->addText('Categoría: ' . ($reporte->categoria->nombre ?? 'Sin categoría'));
        $section->addText('Fecha del Sistema: ' . Carbon::parse($reporte->fecha_sistema)->format('d/m/Y H:i'));
        $section->addText('Fecha del Evento: ' . Carbon::parse($reporte->fecha_evento)->format('d/m/Y H:i'));
        $section->addText('Lugar: ' . $reporte->lugar);
        $section->addText('Estado: ' . ucfirst($reporte->estado));
        
        if($reporte->fecha_aprobacion) {
            $section->addText('Fecha de Aprobación: ' . Carbon::parse($reporte->fecha_aprobacion)->format('d/m/Y H:i'));
            if($reporte->administradorAprobador) {
                $section->addText('Aprobado por: ' . $reporte->administradorAprobador->nombre ?? 'Administrador #' . $reporte->id_administrador_aprobador);
            }
        }
        $section->addTextBreak(1);
        
        // Descripción
        $section->addText('DESCRIPCIÓN DEL HECHO', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
        $section->addText($reporte->descripcion);
        $section->addTextBreak(1);
        
        // Actores Identificados
        if($reporte->actores_identificados) {
            $section->addText('ACTORES IDENTIFICADOS', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
            $section->addText($reporte->actores_identificados);
            $section->addTextBreak(1);
        }
        
        // Campos específicos según categoría
        if($reporte->id_categoria == 6) {
            $section->addText('DETALLES DE LA SITUACIÓN', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
            
            if($reporte->latitud && $reporte->longitud) {
                $section->addText('Coordenadas: ' . $reporte->latitud . ', ' . $reporte->longitud);
            }
            
            if($reporte->numero_personas) {
                $section->addText('Número de Personas: ' . $reporte->numero_personas);
            }
            
            if($reporte->presencia_autoridades) {
                $section->addText('Presencia de Autoridades:');
                $section->addText($reporte->presencia_autoridades);
            }
            
            if($reporte->intervencion_serenazgo) {
                $section->addText('Intervención Serenazgo/PNP:');
                $section->addText($reporte->intervencion_serenazgo);
            }
            $section->addTextBreak(1);
        } else {
            if($reporte->tema_tratado) {
                $section->addText('TEMA TRATADO', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
                $section->addText($reporte->tema_tratado);
                $section->addTextBreak(1);
            }
            
            if($reporte->acuerdos_compromisos) {
                $section->addText('ACUERDOS Y COMPROMISOS', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
                $section->addText($reporte->acuerdos_compromisos);
                $section->addTextBreak(1);
            }
        }
        
        // Recomendación
        if($reporte->recomendacion_preliminar) {
            $section->addText('RECOMENDACIÓN PRELIMINAR', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
            $section->addText($reporte->recomendacion_preliminar);
            $section->addTextBreak(1);
        }
        
        // Enlaces y archivos
        if($reporte->archivos && count($reporte->archivos) > 0) {
            $section->addText('ARCHIVOS Y ENLACES', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
            foreach($reporte->archivos as $archivo) {
                if($archivo->tipo == 'enlace') {
                    $section->addText('Enlace: ' . $archivo->url);
                }
                if($archivo->tipo == 'imagen') {
                    $section->addText('Imagen: ' . $archivo->nombre_archivo);
                }
            }
            $section->addTextBreak(1);
        }

        // Respuestas de Asociados
        if($reporte->respuestasAsociados && count($reporte->respuestasAsociados) > 0) {
            $section->addText('OBSERVACIONES DE ASOCIADOS', ['bold' => true, 'size' => 14, 'color' => 'c0392b']);
            foreach($reporte->respuestasAsociados as $respuesta) {
                $section->addText('Fecha: ' . Carbon::parse($respuesta->fecha_respuesta)->format('d/m/Y H:i'));
                $section->addText('Asociado: ' . ($respuesta->usuario->nombre ?? 'No identificado'));
                $section->addText('Observación: ' . $respuesta->respuesta);
                $section->addTextBreak(1);
            }
        }

        // Footer
        $section->addText('Documento generado el ' . now()->format('d/m/Y H:i:s'), ['size' => 8, 'color' => '666666']);
        $section->addText('DECSAC - Sistema de Monitoreo Social', ['size' => 8, 'color' => '666666']);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'Reporte_' . $reporte->id_reporte . '.docx';
        
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $objWriter->save("php://output");
        exit;
    }

    public function exportPdf($id)
    {
        $reporte = Reporte::with(['categoria', 'archivos'])->findOrFail($id);
        
        $html = view('exports.reporte-pdf', compact('reporte'))->render();
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream('Reporte_' . $reporte->id_reporte . '.pdf');
    }
}