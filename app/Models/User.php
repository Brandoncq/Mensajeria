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

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; 

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'email',
        'dni',
        'username',
        'telefono',
        'contrasena_hash',
        'rol',
        'activo',
    ];

    // ¡Este método debe retornar 'id_usuario'!
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    public function getAuthPassword()
    {
        return $this->contrasena_hash;
    }

    public function reportesAsignados()
    {
        return $this->belongsToMany(
            Reporte::class,
            'reporte_asociados',    // tabla intermedia
            'id_usuario',           // clave foránea que apunta a usuarios
            'id_reporte'            // clave foránea que apunta a reportes
        );
    }
}
