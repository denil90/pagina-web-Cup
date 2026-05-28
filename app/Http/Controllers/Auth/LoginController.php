<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $credenciales['correo'])->first();

        if (!$usuario || !password_verify($credenciales['contrasena'], $usuario->contrasena)) {
            return back()->withErrors([
                'correo' => 'Las credenciales proporcionadas no son correctas.',
            ])->withInput($request->only('correo'));
        }

        Auth::login($usuario);
        $request->session()->regenerate();

        return $this->redirigirSegunRol($usuario);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirigirSegunRol(Usuario $usuario)
    {
        return match ($usuario->rol) {
            'administrador' => redirect()->route('admin.dashboard'),
            'postulante' => redirect()->route('postulante.dashboard'),
            'docente' => redirect()->route('admin.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
