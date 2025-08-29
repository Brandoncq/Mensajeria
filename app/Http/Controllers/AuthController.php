<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            // Redirigir según el rol
            switch ($user->rol) {
                case 'monitor':
                    return redirect()->intended('/dashboardMonitor');
                case 'editor':
                    return redirect()->intended('/dashboardEditor');
                case 'administrador':
                    return redirect()->intended('/dashboardAdministrador');
                default:
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Rol de usuario no autorizado.'
                    ]);
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}