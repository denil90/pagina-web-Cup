<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistroPostulanteRequest;
use App\Models\Carrera;
use App\Models\Gestion;
use App\Models\Postulante;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistroPostulante()
    {
        $carreras = Carrera::all();

        return view('auth.registro', compact('carreras'));
    }

    public function registrarPostulante(RegistroPostulanteRequest $request)
    {
        try {
            DB::beginTransaction();

            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'contrasena' => $request->contrasena,
                'fechanac' => $request->fechanac,
                'sexo' => $request->sexo,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'rol' => 'postulante',
                'correo' => $request->correo,
            ]);

            Postulante::create([
                'id_postulante' => $usuario->id_usuario,
                'colegio_procedencia' => $request->colegio_procedencia,
                'ciudad' => $request->ciudad,
                'titulo_bachiller' => false,
                'libreta_de_ultimo_anio' => false,
                'id_carrera_primera' => $request->id_carrera_primera,
                'id_carrera_segunda' => $request->id_carrera_segunda,
                'id_gestion' => Gestion::max('id_gestion'),
            ]);

            DB::commit();

            Auth::login($usuario);

            return redirect()->route('postulante.dashboard')
                ->with('success', 'Registro exitoso. Bienvenido al sistema CUP.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'general' => 'Error al registrar: ' . $e->getMessage(),
            ]);
        }
    }
}
