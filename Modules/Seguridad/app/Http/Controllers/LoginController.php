<?php

namespace Modules\Seguridad\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Seguridad\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function bienvenida()
    {
        return view('seguridad::bienvenida');
    }

    public function showLoginForm(Request $request)
    {
        $tipo = $request->query('tipo', 'postulante');

        return view('seguridad::login', compact('tipo'));
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

        return redirect()->route('bienvenida');
    }

    private function redirigirSegunRol(Usuario $usuario)
    {
        if ($usuario->rol === 'docente') {
            return redirect()->route('docente.dashboard');
        }

        return match ($usuario->rol) {
            'administrador' => redirect()->route('admin.dashboard'),
            'postulante' => redirect()->route('postulante.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
