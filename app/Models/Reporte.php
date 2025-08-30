<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes'; // Cambia si tu tabla tiene otro nombre

    protected $primaryKey = 'id_reporte';

    protected $fillable = [
        'id_monitor',
        'id_categoria',
        'fecha_sistema',
        'fecha_evento',
        'lugar',
        'descripcion',
        'actores_identificados',
        'estado',
        'latitud',
        'longitud',
        'numero_personas',
        'presencia_autoridades',
        'intervencion_serenazgo',
        'tema_tratado',
        'acuerdos_compromisos',
        'recomendacion_preliminar',
        'fecha_aprobacion',
        'id_administrador_aprobador',
    ];

    public $timestamps = false; // Si no tienes created_at y updated_at

}