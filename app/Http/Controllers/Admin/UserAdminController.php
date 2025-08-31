<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    // Listar usuarios
    public function index()
    {
        $usuarios = User::orderBy('nombre', 'asc')->get();
        $reportes = Reporte::orderBy('fecha_sistema','desc')->get();
        return view('dashboard.administrador', compact('usuarios', 'reportes'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('dashboard.admin.usuarios.create');
    }

    // Almacenar nuevo usuario
    public function store(Request $request)
    {
        // Validación manual para mejor control
        $validator = \Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios',
            'telefono' => 'required|string|max:20|unique:usuarios',
            'dni' => 'required|string|max:15|unique:usuarios',
            'rol' => 'required|in:monitor,administrador,asociado',
            'activo' => 'required|boolean',
        ], [
            'email.unique' => 'El email ya está registrado.',
            'telefono.unique' => 'El teléfono ya está registrado.',
            'dni.unique' => 'El DNI ya está registrado.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            \DB::beginTransaction();

            $username = $this->generarUsername($request->rol, $request->dni);

            $usuario = User::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'dni' => $request->dni,
                'username' => $username,
                'contrasena_hash' => Hash::make($request->telefono),
                'rol' => $request->rol,
                'activo' => $request->activo,
                'fecha_creacion' => now(),
            ]);

            \DB::commit();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario creado correctamente. Username: ' . $username . ', Contraseña: ' . $request->telefono);

        } catch (\Exception $e) {
            \DB::rollBack();

            // Log del error completo para debugging
            \Log::error('Error completo al crear usuario: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            // Manejar errores de duplicidad de la base de datos
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            $mensajeError = 'Error al crear el usuario. ';

            if (str_contains($errorMessage, 'Duplicate entry')) {
                if (str_contains($errorMessage, 'usuarios_email_unique') || str_contains($errorMessage, 'email')) {
                    $mensajeError = 'El email ya está registrado.';
                } elseif (str_contains($errorMessage, 'usuarios_dni_unique') || str_contains($errorMessage, 'dni')) {
                    $mensajeError = 'El DNI ya está registrado.';
                } elseif (str_contains($errorMessage, 'usuarios_telefono_unique') || str_contains($errorMessage, 'telefono')) {
                    $mensajeError = 'El teléfono ya está registrado.';
                } else {
                    $mensajeError = 'Ya existe un usuario con alguno de los datos ingresados.';
                }
            }

            return redirect()->back()
                ->with('error', $mensajeError)
                ->withInput();
        }
    }

    // Ver detalle
    public function show($id)
    {
        $usuario = User::findOrFail($id);
        return view('dashboard.admin.usuarios.show', compact('usuario'));
    }

    // Editar
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('dashboard.admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // Validación manual para mejor control
        $validator = \Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $id . ',id_usuario',
            'telefono' => 'required|string|max:20|unique:usuarios,telefono,' . $id . ',id_usuario',
            'dni' => 'required|string|max:15|unique:usuarios,dni,' . $id . ',id_usuario',
            'rol' => 'required|in:monitor,administrador,asociado',
            'activo' => 'required|boolean',
        ], [
            'email.unique' => 'El email ya está registrado.',
            'telefono.unique' => 'El teléfono ya está registrado.',
            'dni.unique' => 'El DNI ya está registrado.',
        ]);

        // Verificación adicional: si el DNI no cambió, remover la validación unique
        if ($request->dni == $usuario->dni) {
            $validator->sometimes('dni', 'required|string|max:15', function ($input) use ($usuario) {
                return true; // Remueve la validación unique si es el mismo DNI
            });
        }
        if ($request->telefono == $usuario->telefono) {
            $validator->sometimes('telefono', 'required|string|max:20', function ($input) use ($usuario) {
                return true; // Remueve la validación unique si es el mismo DNI
            });
        }
        if ($request->email == $usuario->email) {
            $validator->sometimes('email', 'required|email|max:255', function ($input) use ($usuario) {
                return true; // Remueve la validación unique si es el mismo DNI
            });
        }


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            \DB::beginTransaction();

            // Generar nuevo username basado en rol y DNI
            $nuevoUsername = $this->generarUsername($request->rol, $request->dni);
            
            $data = [
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'dni' => $request->dni,
                'username' => $nuevoUsername,
                'rol' => $request->rol,
                'activo' => $request->activo,
            ];

            // Si cambió el teléfono, actualizar la contraseña también
            if ($usuario->telefono != $request->telefono) {
                $data['contrasena_hash'] = Hash::make($request->telefono);
            }

            $usuario->update($data);

            \DB::commit();

            $mensaje = 'Usuario actualizado correctamente.';
            if ($usuario->telefono != $request->telefono) {
                $mensaje .= ' Nueva contraseña: ' . $request->telefono;
            }

            return redirect()->route('admin.usuarios.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            \DB::rollBack();

            // Log del error completo para debugging
            \Log::error('Error completo al actualizar usuario: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            // Manejar errores de duplicidad de la base de datos
            $errorMessage = $e->getMessage();
            $mensajeError = 'Error al actualizar el usuario. ';

            if (str_contains($errorMessage, 'Duplicate entry')) {
                if (str_contains($errorMessage, 'usuarios_email_unique') || str_contains($errorMessage, 'email')) {
                    $mensajeError = 'El email ya está registrado.';
                } elseif (str_contains($errorMessage, 'usuarios_dni_unique') || str_contains($errorMessage, 'dni')) {
                    $mensajeError = 'El DNI ya está registrado.';
                } elseif (str_contains($errorMessage, 'usuarios_telefono_unique') || str_contains($errorMessage, 'telefono')) {
                    $mensajeError = 'El teléfono ya está registrado.';
                } else {
                    $mensajeError = 'Ya existe un usuario con alguno de los datos ingresados.';
                }
            }

            return redirect()->back()
                ->with('error', $mensajeError)
                ->withInput();
        }
    }

    // Eliminar usuario con todas sus relaciones
    // Cambiar estado de usuario en vez de eliminar
    public function destroy($id)
    {
        try {
            $usuario = User::findOrFail($id);

            // Alternar el estado
            $usuario->activo = !$usuario->activo;
            $usuario->save();

            $estado = $usuario->activo ? 'activado' : 'desactivado';

            return redirect()->route('admin.usuarios.index')
                ->with('success', "Usuario {$estado} correctamente.");

        } catch (\Exception $e) {
            \Log::error('Error al cambiar estado del usuario: ' . $e->getMessage());
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'Error al cambiar el estado del usuario: ' . $e->getMessage());
        }
    }


    // Método para generar username según el formato requerido
    private function generarUsername($rol, $dni)
    {
        $prefijo = match($rol) {
            'monitor' => 'MsMonitorSocial',
            'administrador' => 'ADM',
            'asociado' => 'ASC',
            default => 'USER'
        };

        return $prefijo . '-' . $dni;
    }

    // Método opcional para resetear contraseña
    public function resetPassword($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $nuevaContrasena = $usuario->telefono;
            $usuario->contrasena_hash = Hash::make($nuevaContrasena);
            $usuario->save();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Contraseña reseteada correctamente. Nueva contraseña: ' . $nuevaContrasena);

        } catch (\Exception $e) {
            \Log::error('Error al resetear contraseña: ' . $e->getMessage());
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'Error al resetear la contraseña: ' . $e->getMessage());
        }
    }
}