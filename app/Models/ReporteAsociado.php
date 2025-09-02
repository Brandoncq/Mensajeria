<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteAsociado extends Model
{
    use HasFactory;

    protected $table = 'reporte_asociados';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_reporte',
        'id_usuario',
        'fecha_asignacion'
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