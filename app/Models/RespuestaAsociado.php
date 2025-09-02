<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaAsociado extends Model
{
    use HasFactory;

    protected $table = 'respuestas_asociados';
    protected $primaryKey = 'id_respuesta';
    public $timestamps = false;

    protected $fillable = [
        'id_reporte',
        'id_usuario',
        'respuesta',
        'fecha_respuesta'
    ];

    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'id_reporte', 'id_reporte');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}