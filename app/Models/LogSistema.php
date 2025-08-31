<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    use HasFactory;

    protected $table = 'logs_sistema';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_usuario',
        'accion',
        'detalles',
        'fecha_log',
        'ip_origen'
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}