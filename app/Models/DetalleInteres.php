<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleInteres extends Model
{
    use HasFactory;

    protected $table = 'detallearea';
    protected $primaryKey = 'id_area_interes';
    public $timestamps = false;

    protected $fillable = [
        'id_area_interes',
        'id_usuario',
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
