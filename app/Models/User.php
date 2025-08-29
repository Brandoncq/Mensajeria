<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nombre real de la tabla
    protected $table = 'usuarios';

    // Clave primaria personalizada
    protected $primaryKey = 'id_usuario';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'contrasena_hash',
        'rol',
        'activo',
    ];

    // Campos ocultos al serializar
    protected $hidden = [
        'contrasena_hash',
    ];

    // Casts
    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'fecha_ultimo_login' => 'datetime',
        ];
    }

    // Importante: Laravel usa "password" por defecto, lo mapeamos
    public function getAuthPassword()
    {
        return $this->contrasena_hash;
    }
}
