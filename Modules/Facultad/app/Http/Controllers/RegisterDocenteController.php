<?php

namespace Modules\Facultad\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Facultad\Http\Requests\RegistroDocentePublicoRequest;
use Modules\Facultad\Models\Docente;
use Modules\Seguridad\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterDocenteController extends Controller
{
    public function showRegistro()
    {
        return view('facultad::registro-docente');
    }

    public function registrar(RegistroDocentePublicoRequest $request)
    {
        try {
            DB::beginTransaction();

            $usuario = Usuario::create([
                'nombre'    => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci'        => $request->ci,
                'contrasena'=> $request->contrasena,
                'fechanac'  => $request->fechanac,
                'sexo'      => $request->sexo,
                'direccion' => $request->direccion,
                'telefono'  => $request->telefono,
                'rol'       => 'docente',
                'correo'    => $request->correo,
            ]);

            Docente::create([
                'id_docente'          => $usuario->id_usuario,
                'titulo_profesional'  => null,
                'maestria'            => null,
                'diplomado'           => null,
                'estado'              => 'PENDIENTE',
            ]);

            DB::commit();

            Auth::login($usuario);

            return redirect()->route('docente.dashboard')
                ->with('success', 'Registro exitoso. Bienvenido al sistema CUP. Su postulación está pendiente de revisión.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'general' => 'Error al registrar: ' . $e->getMessage(),
            ]);
        }
    }
}
