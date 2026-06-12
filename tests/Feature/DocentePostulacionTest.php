<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Modules\Seguridad\Models\Usuario;
use Modules\Facultad\Models\Docente;

class DocentePostulacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_registro_docente_view_is_accessible()
    {
        $response = $this->get(route('registro.docente'));
        $response->assertStatus(200);
        $response->assertSee('Registro de Docente');
    }

    public function test_docente_can_self_register_and_upload_documents()
    {
        $data = [
            'nombre' => 'Pedro',
            'apellidos' => 'Ramirez',
            'ci' => '9876543',
            'contrasena' => 'password123',
            'contrasena_confirmation' => 'password123',
            'fechanac' => '1980-05-15',
            'sexo' => 'M',
            'direccion' => 'Av. Las Americas 123',
            'telefono' => '78945612',
            'correo' => 'pedro.ramirez@example.com',
        ];

        $response = $this->post(route('registro.docente'), $data);

        $response->assertRedirect(route('docente.dashboard'));

        // Verificar que el usuario fue creado
        $this->assertDatabaseHas('usuario', [
            'correo' => 'pedro.ramirez@example.com',
            'rol' => 'docente',
        ]);

        $usuario = Usuario::where('correo', 'pedro.ramirez@example.com')->first();
        $this->assertNotNull($usuario);

        // Verificar que el docente fue creado con estado PENDIENTE y campos profesionales nulos
        $this->assertDatabaseHas('docente', [
            'id_docente' => $usuario->id_usuario,
            'titulo_profesional' => null,
            'estado' => 'PENDIENTE',
        ]);

        // Autenticar como el nuevo docente para subir requisitos
        $this->actingAs($usuario);

        // Simular la carga de un PDF de título profesional con el nombre del título
        $file = \Illuminate\Http\UploadedFile::fake()->create('titulo.pdf', 500, 'application/pdf');

        $responseUpload = $this->post(route('docente.requisitos.titulo'), [
            'titulo_profesional' => 'Lic. en Ciencias de la Computacion',
            'archivo_titulo' => $file,
        ]);

        $responseUpload->assertRedirect(); // redirige de vuelta
        
        // Verificar que el título y la ruta del archivo se actualizaron en la BD
        $docente = $usuario->fresh()->docente;
        $this->assertEquals('Lic. en Ciencias de la Computacion', $docente->titulo_profesional);
        $this->assertNotNull($docente->archivo_titulo);
    }

    public function test_pending_docente_is_restricted_from_admin_area()
    {
        // Crear un usuario docente con estado PENDIENTE
        $usuario = Usuario::create([
            'nombre' => 'Carlos',
            'apellidos' => 'Paz',
            'ci' => '445566',
            'contrasena' => 'password123',
            'fechanac' => '1985-02-20',
            'sexo' => 'M',
            'direccion' => 'Calle Falsa 123',
            'rol' => 'docente',
            'correo' => 'carlos@example.com',
        ]);

        Docente::create([
            'id_docente' => $usuario->id_usuario,
            'titulo_profesional' => 'Ingeniero Mecanico',
            'estado' => 'PENDIENTE',
        ]);

        $this->actingAs($usuario);

        // Intentar acceder a la ruta de administración de docentes
        $response = $this->get(route('admin.docentes.index'));

        // Debe redirigir a su propio dashboard docente
        $response->assertRedirect(route('docente.dashboard'));
    }

    public function test_admin_can_approve_docente()
    {
        // Crear administrador
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellidos' => 'General',
            'ci' => '111111',
            'contrasena' => 'adminpass',
            'fechanac' => '1975-01-01',
            'sexo' => 'M',
            'direccion' => 'Edificio Central',
            'rol' => 'administrador',
            'correo' => 'admin@ficct.com',
        ]);

        // Crear docente PENDIENTE
        $usuarioDocente = Usuario::create([
            'nombre' => 'Laura',
            'apellidos' => 'Torres',
            'ci' => '888999',
            'contrasena' => 'password123',
            'fechanac' => '1988-08-08',
            'sexo' => 'F',
            'direccion' => 'Calle 4 Oeste',
            'rol' => 'docente',
            'correo' => 'laura@example.com',
        ]);

        $docente = Docente::create([
            'id_docente' => $usuarioDocente->id_usuario,
            'titulo_profesional' => 'Lic. en Matematicas',
            'estado' => 'PENDIENTE',
        ]);

        $this->actingAs($admin);

        // Aprobar la postulación
        $response = $this->put(route('admin.docentes.aprobar', $docente->id_docente));

        $response->assertRedirect(route('admin.docentes.show', $docente->id_docente));

        // Verificar cambio de estado a ACTIVO
        $this->assertEquals('ACTIVO', $docente->fresh()->estado);
    }

    public function test_active_docente_can_manage_their_own_grades_but_not_others()
    {
        // 1. Crear docente ACTIVO
        $usuarioDocente = Usuario::create([
            'nombre' => 'Marcos',
            'apellidos' => 'Suarez',
            'ci' => '121212',
            'contrasena' => 'password123',
            'fechanac' => '1980-01-01',
            'sexo' => 'M',
            'direccion' => 'Calle Central',
            'rol' => 'docente',
            'correo' => 'marcos@example.com',
        ]);

        $docente = Docente::create([
            'id_docente' => $usuarioDocente->id_usuario,
            'titulo_profesional' => 'Ingeniero de Sistemas',
            'estado' => 'ACTIVO',
        ]);

        // Crear una materia y un grupo de prueba
        $materia = \Modules\Academico\Models\Materia::create([
            'nombre' => 'Programacion I',
            'porcentaje_examen1' => 30,
            'porcentaje_examen2' => 30,
            'porcentaje_examen3' => 40,
        ]);

        $grupo = \Modules\Planificacion\Models\Grupo::create([
            'nombre' => 'Grupo SF',
            'capacidad_maxima' => 40,
        ]);

        // Asignar el grupo al docente
        DB::table('docente_grupo')->insert([
            'id_docente' => $docente->id_docente,
            'id_grupo' => $grupo->id_grupo,
            'id_materia' => $materia->id_materia,
        ]);

        // Crear otro grupo al que no está asignado
        $grupoAjeno = \Modules\Planificacion\Models\Grupo::create([
            'nombre' => 'Grupo AJ',
            'capacidad_maxima' => 40,
        ]);

        // Loguearse como el docente activo
        $this->actingAs($usuarioDocente);

        // 2. Intentar entrar a su grupo asignado -> Debe poder entrar
        $response = $this->get(route('docente.grupos.materia.estudiantes', [$grupo->id_grupo, $materia->id_materia]));
        $response->assertStatus(200);

        // 3. Intentar entrar a un grupo ajeno -> Debe dar 403 Forbidden
        $responseAjena = $this->get(route('docente.grupos.materia.estudiantes', [$grupoAjeno->id_grupo, $materia->id_materia]));
        $responseAjena->assertStatus(403);

        // 4. Intentar acceder a rutas administrativas -> Debe ser redirigido
        $responseAdmin = $this->get(route('admin.docentes.index'));
        $responseAdmin->assertRedirect(route('docente.dashboard'));
    }
}
