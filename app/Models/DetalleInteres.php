<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleInteres extends Model
{
    use HasFactory;

    protected $table = 'detallearea';
    
    // NO tiene clave primaria autoincremental (es clave compuesta)
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Sin clave primaria única, usa clave compuesta
    protected $primaryKey = null;
    
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_area_interes',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function area()
    {
        return $this->belongsTo(AreaInteres::class, 'id_area_interes', 'id_area_interes');
    }
}
