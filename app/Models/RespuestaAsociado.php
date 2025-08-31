<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaAsociado extends Model
{
    use HasFactory;

    protected $table = 'respuestas_asociados';
    protected $primaryKey = 'id_respuesta';

    protected $fillable = [
        'id_reporte',
        'id_usuario',
        'respuesta',
        'fecha_respuesta'
    ];

    public $timestamps = false;

    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'id_reporte');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}