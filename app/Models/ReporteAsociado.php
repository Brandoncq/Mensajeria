<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteAsociado extends Model
{
    use HasFactory;

    protected $table = 'reporte_asociados';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_reporte',
        'id_usuario',
        'fecha_asignacion'
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