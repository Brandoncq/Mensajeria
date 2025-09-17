<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        Log::info('Mostrando formulario de login.');
        if (Auth::check()) {
            $user = Auth::user();
            Log::info('Usuario ya autenticado.', ['id' => $user->id ?? null, 'dni' => $user->dni ?? null, 'rol' => $user->rol ?? null]);
            switch ($user->rol) {
                case 'monitor':
                    Log::info('Redirigiendo a dashboardMonitor.');
                    return redirect('/dashboardMonitor');
                case 'asociado':
                    Log::info('Redirigiendo a dashboardEditor.');
                    return redirect('/dashboardEditor');
                case 'administrador':
                    Log::info('Redirigiendo a dashboardAdministrador.');
                    return redirect('/dashboardAdministrador');
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Intentando login.', ['dni' => $request->input('dni')]);
        $credentials = $request->only('dni', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            Log::info('Login exitoso.', ['id' => $user->id ?? null, 'dni' => $user->dni ?? null, 'rol' => $user->rol ?? null]);
            // Redirigir según el rol
            switch ($user->rol) {
                case 'monitor':
                    Log::info('Redirigiendo a dashboardMonitor.');
                    return redirect()->intended('/dashboardMonitor');
                case 'asociado':
                    Log::info('Redirigiendo a dashboardEditor.');
                    return redirect()->intended('/dashboardEditor');
                case 'administrador':
                    Log::info('Redirigiendo a dashboardAdministrador.');
                    return redirect()->intended('/dashboardAdministrador');
                default:
                    Log::warning('Rol de usuario no autorizado.', ['rol' => $user->rol]);
                    Auth::logout();
                    return back()->withErrors([
                        'dni' => 'Rol de usuario no autorizado.'
                    ]);
            }
        }

        Log::warning('Login fallido.', ['dni' => $credentials['dni']]);
        return back()->withErrors([
            'dni' => 'Las credenciales no son correctas.',
        ]);
    }

    public function logout(Request $request)
    {
        Log::info('Cerrando sesión de usuario.', ['id' => Auth::id()]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('Sesión cerrada y tokens regenerados.');
        return redirect('/');
    }
}