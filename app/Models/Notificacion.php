<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';

    protected $fillable = [
        'id_reporte',
        'id_usuario_destino',
        'tipo',
        'contenido',
        'fecha_envio',
        'estado',
        'intentos',
        'error_mensaje'
    ];

    public $timestamps = false;

    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'id_reporte');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_destino');
    }
}