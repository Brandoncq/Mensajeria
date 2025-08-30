<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;

    protected $table = 'archivos_adjuntos';
    protected $primaryKey = 'id_archivo';

    protected $fillable = [
        'id_reporte',
        'tipo', // 'imagen' o 'enlace'
        'url',
        'nombre_archivo',
        'fecha_subida',
    ];

    public $timestamps = false;

    // Relación con reporte
    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'id_reporte');
    }

    // Relación con archivos
    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'id_reporte');
    }
}