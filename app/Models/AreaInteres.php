<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaInteres extends Model
{
    use HasFactory;

    // Nombre de la tabla (si no sigue convención plural)
    protected $table = 'areainteres';

    // Clave primaria
    protected $primaryKey = 'id_area_interes';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'nombre',
    ];

    // Si no tienes created_at ni updated_at
    public $timestamps = false;
    
     public function detalles()
    {
        return $this->hasMany(DetalleInteres::class, 'id_area_interes', 'id_area_interes');
    }
}
