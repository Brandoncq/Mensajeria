<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EdicionReporte extends Model
{
    use HasFactory;

    protected $table = 'ediciones_reporte';
    protected $primaryKey = 'id_edicion';

    protected $fillable = [
        'id_reporte',
        'id_usuario_editor',
        'fecha_edicion',
        'cambios'
    ];

    public $timestamps = false;

    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'id_reporte');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_editor');
    }
}