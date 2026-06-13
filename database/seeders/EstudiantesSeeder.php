<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EstudiantesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener o crear las 3 gestiones requeridas: 2-2024, 1-2025, 2-2025
        $gestiones = [
            '2-2024' => ['semestre' => '2', 'anio' => 2024],
            '1-2025' => ['semestre' => '1', 'anio' => 2025],
            '2-2025' => ['semestre' => '2', 'anio' => 2025],
        ];

        $gestionIds = [];
        foreach ($gestiones as $key => $gInfo) {
            $g = DB::table('gestion')
                ->where('semestre', $gInfo['semestre'])
                ->where('anio', $gInfo['anio'])
                ->first();

            if (!$g) {
                $gestionIds[$key] = DB::table('gestion')->insertGetId([
                    'semestre' => $gInfo['semestre'],
                    'anio' => $gInfo['anio']
                ], 'id_gestion');
            } else {
                $gestionIds[$key] = $g->id_gestion;
            }
        }

        // 2. Obtener o crear los 3 turnos
        $turnosPredefinidos = ['Mañana', 'Tarde', 'Noche'];
        $turnoIds = [];
        foreach ($turnosPredefinidos as $tName) {
            $t = DB::table('turno')->where('nombre', $tName)->first();
            if (!$t) {
                $turnoIds[$tName] = DB::table('turno')->insertGetId(['nombre' => $tName], 'id_turno');
            } else {
                $turnoIds[$tName] = $t->id_turno;
            }
        }

        // 3. Obtener o crear las aulas necesarias
        $aulas = DB::table('aula')->pluck('id_aula')->toArray();
        if (empty($aulas)) {
            $aulas[] = DB::table('aula')->insertGetId([
                'nombre' => 'Aula 101',
                'edificio' => 'Edificio A',
                'capacidad' => 70
            ], 'id_aula');
            $aulas[] = DB::table('aula')->insertGetId([
                'nombre' => 'Aula 102',
                'edificio' => 'Edificio A',
                'capacidad' => 70
            ], 'id_aula');
        }

        // Limpiar únicamente datos de postulantes sembrados anteriormente para asegurar una siembra limpia sin afectar datos reales
        $seededUserIds = DB::table('usuario')
            ->where('correo', 'LIKE', '%@postulante.cup.edu')
            ->pluck('id_usuario')
            ->toArray();

        if (!empty($seededUserIds)) {
            DB::table('admision_final')->whereIn('id_postulante', $seededUserIds)->delete();
            DB::table('notas')->whereIn('id_postulante', $seededUserIds)->delete();
            DB::table('pago')->whereIn('id_postulante', $seededUserIds)->delete();
            DB::table('postulante')->whereIn('id_postulante', $seededUserIds)->delete();
            DB::table('usuario')->whereIn('id_usuario', $seededUserIds)->delete();
        }

        // 4. Asegurar que existan las aulas adicionales solicitadas (Mañana: 13-17, Tarde: 23-27, Noche: 33-37)
        $aulasAdicionales = [
            'Mañana' => [13, 14, 15, 16, 17],
            'Tarde' => [23, 24, 25, 26, 27],
            'Noche' => [33, 34, 35, 36, 37],
        ];
        foreach ($aulasAdicionales as $tNombre => $ids) {
            foreach ($ids as $id) {
                if (!DB::table('aula')->where('id_aula', $id)->exists()) {
                    DB::table('aula')->insert([
                        'id_aula' => $id,
                        'nombre' => 'Aula ' . $id,
                        'edificio' => 'Edificio Adicional',
                        'capacidad' => 70
                    ]);
                }
            }
        }

        // 5. Asegurar que existan los 6 grupos lógicos iniciales y tengan capacidad de 70 alumnos
        $grupoNombres = ['M1', 'M2', 'T1', 'T2', 'N1', 'N2'];
        $grupos = [];
        foreach ($grupoNombres as $gNombre) {
            $gr = DB::table('grupo')->where('nombre', $gNombre)->first();

            // Determinar el turno correspondiente
            if (str_starts_with($gNombre, 'M')) {
                $tId = $turnoIds['Mañana'];
            } elseif (str_starts_with($gNombre, 'T')) {
                $tId = $turnoIds['Tarde'];
            } else {
                $tId = $turnoIds['Noche'];
            }

            if (!$gr) {
                $grId = DB::table('grupo')->insertGetId([
                    'nombre' => $gNombre,
                    'capacidad_maxima' => 70,
                    'id_aula' => $aulas[array_rand($aulas)],
                    'id_turno' => $tId,
                ], 'id_grupo');
            } else {
                $grId = $gr->id_grupo;
                // Forzar capacidad máxima a 70
                DB::table('grupo')->where('id_grupo', $grId)->update([
                    'capacidad_maxima' => 70
                ]);
            }
            $grupos[$gNombre] = $grId;
        }

        // 6. Asegurar que tengamos al menos 8 docentes en la BD
        $docenteIds = DB::table('docente')->pluck('id_docente')->toArray();
        if (count($docenteIds) < 8) {
            $needed = 8 - count($docenteIds);
            for ($i = 1; $i <= $needed; $i++) {
                $ci = '4000' . rand(100, 999) . $i;
                $userId = DB::table('usuario')->insertGetId([
                    'nombre' => 'Docente' . $i,
                    'apellidos' => 'ApellidoDocente' . $i,
                    'ci' => $ci,
                    'contrasena' => Hash::make('docente123'),
                    'fechanac' => '1982-05-14',
                    'sexo' => ($i % 2 == 0) ? 'M' : 'F',
                    'direccion' => 'Av. Busch S/N',
                    'telefono' => '789456' . $i,
                    'rol' => 'docente',
                    'correo' => 'docente' . $i . '_' . Str::random(3) . '@cup.edu',
                ], 'id_usuario');
                DB::table('docente')->insert([
                    'id_docente' => $userId,
                    'titulo_profesional' => 'Licenciado en Ciencias',
                    'estado' => 'ACTIVO',
                ]);
                $docenteIds[] = $userId;
            }
        }

        // 7. Configurar o crear las materias necesarias
        $materiaNombres = [
            1 => 'Computación',
            2 => 'Matemáticas',
            3 => 'Inglés',
            4 => 'Física'
        ];
        foreach ($materiaNombres as $mId => $mName) {
            $mat = DB::table('materia')->where('id_materia', $mId)->first();
            if (!$mat) {
                DB::table('materia')->insert([
                    'id_materia' => $mId,
                    'nombre' => $mName,
                    'porcentaje_examen1' => 33.33,
                    'porcentaje_examen2' => 33.33,
                    'porcentaje_examen3' => 33.34,
                ]);
            }
        }

        // 8. Asegurar que los horarios de 1 hora existan en la BD (para Lunes)
        $diasSemana = ['Lunes'];
        $bloquesHorarios = [
            'Mañana' => [
                ['inicio' => '07:00:00', 'fin' => '08:00:00'],
                ['inicio' => '08:00:00', 'fin' => '09:00:00'],
                ['inicio' => '09:00:00', 'fin' => '10:00:00'],
                ['inicio' => '10:00:00', 'fin' => '11:00:00'],
            ],
            'Tarde' => [
                ['inicio' => '14:00:00', 'fin' => '15:00:00'],
                ['inicio' => '15:00:00', 'fin' => '16:00:00'],
                ['inicio' => '16:00:00', 'fin' => '17:00:00'],
                ['inicio' => '17:00:00', 'fin' => '18:00:00'],
            ],
            'Noche' => [
                ['inicio' => '19:00:00', 'fin' => '20:00:00'],
                ['inicio' => '20:00:00', 'fin' => '21:00:00'],
                ['inicio' => '21:00:00', 'fin' => '22:00:00'],
                ['inicio' => '22:00:00', 'fin' => '23:00:00'],
            ]
        ];

        $horarioIds = [];
        foreach ($bloquesHorarios as $turnoName => $bloques) {
            foreach ($bloques as $idx => $b) {
                $h = DB::table('horario')
                    ->where('dia', 'Lunes')
                    ->where('hora_inicio', $b['inicio'])
                    ->first();

                if (!$h) {
                    $hId = DB::table('horario')->insertGetId([
                        'dia' => 'Lunes',
                        'hora_inicio' => $b['inicio'],
                        'hora_final' => $b['fin']
                    ], 'id_horario');
                } else {
                    $hId = $h->id_horario;
                }
                $horarioIds[$turnoName][$idx] = $hId;
            }
        }

        // Closures para asignación dinámica de Docentes y Grupos
        $getOrCreateAvailableDocente = function ($horarioId) use (&$docenteIds) {
            foreach ($docenteIds as $dId) {
                $hasClash = DB::table('docente_grupo')
                    ->where('id_docente', $dId)
                    ->where('id_horario', $horarioId)
                    ->exists();

                if ($hasClash) {
                    continue;
                }

                $count = DB::table('docente_grupo')
                    ->where('id_docente', $dId)
                    ->count();

                if ($count < 3) {
                    return $dId;
                }
            }

            // Si no hay docentes disponibles sin choque y con menos de 3 materias, crear uno nuevo
            $newIndex = DB::table('docente')->count() + 1;
            $ci = '4000' . rand(100, 999) . $newIndex;
            $userId = DB::table('usuario')->insertGetId([
                'nombre' => 'Docente' . $newIndex,
                'apellidos' => 'ApellidoDocente' . $newIndex,
                'ci' => $ci,
                'contrasena' => Hash::make('docente123'),
                'fechanac' => '1982-05-14',
                'sexo' => ($newIndex % 2 == 0) ? 'M' : 'F',
                'direccion' => 'Av. Busch S/N',
                'telefono' => '789456' . $newIndex,
                'rol' => 'docente',
                'correo' => 'docente' . $newIndex . '_' . Str::random(3) . '@cup.edu',
            ], 'id_usuario');
            DB::table('docente')->insert([
                'id_docente' => $userId,
                'titulo_profesional' => 'Licenciado en Ciencias',
                'estado' => 'ACTIVO',
            ]);
            $docenteIds[] = $userId;
            return $userId;
        };

        // Asegurar que los grupos iniciales tengan asignación de docentes si el ambiente está vacío
        foreach ($grupoNombres as $gNombre) {
            $grId = $grupos[$gNombre];
            $hasAssignments = DB::table('docente_grupo')->where('id_grupo', $grId)->exists();
            if (!$hasAssignments) {
                $turnoName = str_starts_with($gNombre, 'M') ? 'Mañana' : (str_starts_with($gNombre, 'T') ? 'Tarde' : 'Noche');
                $materiaHorarios = [
                    3 => $horarioIds[$turnoName][0], // Inglés -> Block 1
                    1 => $horarioIds[$turnoName][1], // Computación -> Block 2
                    4 => $horarioIds[$turnoName][2], // Física -> Block 3
                    2 => $horarioIds[$turnoName][3], // Matemáticas -> Block 4
                ];
                foreach ($materiaHorarios as $mId => $hId) {
                    $docenteId = $getOrCreateAvailableDocente($hId);
                    DB::table('docente_grupo')->insert([
                        'id_docente' => $docenteId,
                        'id_grupo' => $grId,
                        'id_materia' => $mId,
                        'id_horario' => $hId
                    ]);
                }
            }
        }

        $getOrCreateAvailableGroup = function ($turnoName) use ($turnoIds, $horarioIds, $getOrCreateAvailableDocente, $aulas) {
            $tId = $turnoIds[$turnoName];

            // Buscar un grupo existente con espacio disponible (inscritos < 70)
            $grupos = DB::table('grupo')->where('id_turno', $tId)->orderBy('id_grupo')->get();
            foreach ($grupos as $gr) {
                $inscritos = DB::table('postulante')->where('id_grupo', $gr->id_grupo)->count();
                if ($inscritos < 70) {
                    return $gr->id_grupo;
                }
            }

            // Crear nuevo grupo si todos están llenos
            $prefix = ($turnoName === 'Mañana') ? 'M' : (($turnoName === 'Tarde') ? 'T' : 'N');
            $existingNames = DB::table('grupo')
                ->where('nombre', 'like', $prefix . '%')
                ->pluck('nombre')
                ->toArray();

            $maxNum = 2; // Partimos de M3, T3, N3
            foreach ($existingNames as $name) {
                $num = (int) substr($name, 1);
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
            $nextNum = $maxNum + 1;
            $newGroupName = $prefix . $nextNum;

            // Rango de aulas por turno
            if ($turnoName === 'Mañana') {
                $aulaStart = 13;
            } elseif ($turnoName === 'Tarde') {
                $aulaStart = 23;
            } else {
                $aulaStart = 33;
            }
            $aulaId = $aulaStart + (($nextNum - 3) % 5);

            $grId = DB::table('grupo')->insertGetId([
                'nombre' => $newGroupName,
                'capacidad_maxima' => 70,
                'id_aula' => $aulaId,
                'id_turno' => $tId,
            ], 'id_grupo');

            // Asignar docentes a este nuevo grupo
            $materiaHorarios = [
                3 => $horarioIds[$turnoName][0], // Inglés
                1 => $horarioIds[$turnoName][1], // Computación
                4 => $horarioIds[$turnoName][2], // Física
                2 => $horarioIds[$turnoName][3], // Matemáticas
            ];
            foreach ($materiaHorarios as $mId => $hId) {
                $docenteId = $getOrCreateAvailableDocente($hId);
                DB::table('docente_grupo')->insert([
                    'id_docente' => $docenteId,
                    'id_grupo' => $grId,
                    'id_materia' => $mId,
                    'id_horario' => $hId
                ]);
            }

            return $grId;
        };


        // 9. Población de Estudiantes (Postulantes)
        // Generaremos 600 estudiantes por gestión.
        $nombresMasculinos = [
            'Alejandro', 'Andrés', 'Carlos', 'Cristian', 'Daniel', 'David', 'Diego', 
            'Eduardo', 'Fernando', 'Gabriel', 'Guillermo', 'Javier', 'Jorge', 'José', 
            'Juan', 'Julio', 'Luis', 'Manuel', 'Mario', 'Mauricio', 'Miguel', 'Oscar', 
            'Pablo', 'Pedro', 'Ricardo', 'Roberto', 'Rodrigo', 'Santiago', 'Sebastián', 
            'Víctor', 'Walter', 'Adrián', 'Alan', 'Alberto', 'Álvaro', 'Antonio', 
            'Armando', 'Arturo', 'Benjamín', 'Braulio', 'Bruno', 'Camilo', 'César', 
            'Claudio', 'Clemente', 'Cristóbal', 'Damián', 'Darío', 'Denis', 'Edgar', 
            'Edison', 'Edwin', 'Elías', 'Emiliano', 'Emilio', 'Enrique', 'Eric', 
            'Ernesto', 'Esteban', 'Ezequiel', 'Fabián', 'Federico', 'Felipe', 
            'Félix', 'Francisco', 'Gerardo', 'Gerson', 'Gonzalo', 'Gregorio', 
            'Gustavo', 'Héctor', 'Hernán', 'Homero', 'Horacio', 'Hugo', 'Ignacio', 
            'Iván', 'Jesús', 'Joaquín', 'Jonathan', 'Jordán', 'Josué', 'Julián', 
            'Leonardo', 'Lorenzo', 'Lucas', 'Marcelo', 'Marco', 'Marcos', 'Martín', 
            'Mateo', 'Matías', 'Moisés', 'Nicolás', 'Noé', 'Oliver', 'Orlando', 
            'Patricio', 'Rafael', 'Ramón', 'Raúl', 'René', 'Rubén', 'Samuel', 
            'Saúl', 'Sergio', 'Tomás', 'Valentín', 'Vicente'
        ];

        $nombresFemeninos = [
            'Alejandra', 'Andrea', 'Camila', 'Carolina', 'Claudia', 'Daniela', 'Diana', 
            'Elizabeth', 'Estefanía', 'Gabriela', 'Isabel', 'Jessica', 'Laura', 'Leticia', 
            'Lorena', 'Lucía', 'María', 'Natalia', 'Patricia', 'Paola', 'Raquel', 'Sara', 
            'Sofía', 'Valeria', 'Vanessa', 'Verónica', 'Adriana', 'Alba', 'Alexandra', 
            'Alicia', 'Alondra', 'Ana', 'Angélica', 'Antonia', 'Ariana', 'Beatriz', 
            'Brenda', 'Carla', 'Cecilia', 'Cindy', 'Cristina', 'Débora', 'Denisse', 
            'Dolores', 'Elena', 'Elisa', 'Eliana', 'Emilia', 'Erika', 'Esperanza', 
            'Eva', 'Fabiana', 'Fernanda', 'Francisca', 'Geraldine', 'Gloria', 'Graciela', 
            'Guadalupe', 'Helen', 'Iliana', 'Irene', 'Irina', 'Jackeline', 'Jacqueline', 
            'Jazmín', 'Jennifer', 'Jimena', 'Johana', 'Josefina', 'Julieta', 'Karen', 
            'Katherine', 'Kenia', 'Liliana', 'Lina', 'Lissa', 'Lizeth', 'Lourdes', 
            'Luisa', 'Magaly', 'Maira', 'Marcela', 'Margarita', 'Mariana', 'Marisol', 
            'Marta', 'Melanie', 'Melissa', 'Mercedes', 'Micaela', 'Mónica', 'Nadia', 
            'Nancy', 'Nicole', 'Noelia', 'Olivia', 'Pamela', 'Paula', 'Rocío', 'Rosa', 
            'Rosario', 'Rosaura', 'Ruth', 'Sabrina', 'Samanta', 'Silvia', 'Susana', 
            'Tamara', 'Tania', 'Teresa', 'Valentina', 'Viviana', 'Yesica', 'Zulma'
        ];
        $apellidos = [
            'Quispe', 'Mamani', 'Condori', 'Choque', 'Flores', 'Rodríguez', 'Vargas', 
            'Guzmán', 'Rojas', 'Ortiz', 'Sánchez', 'Chávez', 'Ramos', 'Torrez', 
            'Gutiérrez', 'Castro', 'Romero', 'Fernández', 'Suárez', 'Justiniano', 
            'Cuéllar', 'Aguilera', 'Vaca', 'Pinto', 'Mercado', 'Roca', 'Hurtado', 
            'Soliz', 'Mendoza', 'Peralta', 'Montaño', 'Llanos', 'Chari', 'Arias', 
            'Barrios', 'Cabrera', 'Calderón', 'Camacho', 'Cano', 'Cárdenas', 'Cortez', 
            'Cruz', 'Delgado', 'Díaz', 'Durán', 'Escobar', 'Espinoza', 'Figueroa', 
            'Fuentes', 'Galindo', 'García', 'Gómez', 'Gonzales', 'Guarachi', 'Herrera', 
            'Ibañez', 'Jiménez', 'Lara', 'López', 'Luna', 'Martínez', 'Medina', 
            'Miranda', 'Molina', 'Morales', 'Moreno', 'Muñoz', 'Navarro', 'Núñez', 
            'Orellana', 'Paredes', 'Paz', 'Peña', 'Pérez', 'Ramírez', 'Reyes', 'Ríos', 
            'Rivera', 'Robles', 'Sáenz', 'Salazar', 'Silva', 'Soria', 'Sosa', 'Tapia', 
            'Tarqui', 'Torrico', 'Ulloa', 'Urquizo', 'Valdez', 'Valencia', 'Vera', 
            'Villalobos', 'Villarroel', 'Zabala', 'Zambrana', 'Zapata',
            'Acosta', 'Aguilar', 'Alaniz', 'Alarcón', 'Álvarez', 'Amador', 'Andrade', 
            'Antelo', 'Aponte', 'Aquino', 'Arce', 'Aranda', 'Arauz', 'Arciénaga', 
            'Ardaya', 'Arenas', 'Arízaga', 'Arteaga', 'Asbún', 'Avendaño', 'Avilés', 
            'Ayala', 'Baptista', 'Beltrán', 'Benavides', 'Benítez', 'Berríos', 
            'Blanco', 'Bonilla', 'Borda', 'Bravo', 'Bustamante', 'Caballero', 
            'Cáceres', 'Cadima', 'Calvo', 'Campero', 'Campos', 'Cantú', 'Capriles', 
            'Carballo', 'Cardona', 'Carvajal', 'Casanova', 'Castellón', 'Castillo', 
            'Céspedes', 'Claure', 'Colque', 'Contreras', 'Cornejo', 'Correa', 
            'Cossío', 'Crespo', 'Cuenca', 'Dávalos', 'Daza', 'Denis', 'Dorado', 
            'Duarte', 'Equiza', 'Ergueta', 'Escóbar', 'Espada', 'Estrada', 'Fariñas', 
            'Ferrufino', 'Fierro', 'Franco', 'Frías', 'Gandarillas', 'Garay', 
            'Garrido', 'Godoy', 'Goitia', 'Guevara', 'Hinojosa', 'Ibáñez', 'Ibarra', 
            'Ingavi', 'Iturri', 'Jaime', 'Jaldín', 'Jordán', 'Landaeta', 'Larrea', 
            'León', 'Lima', 'Linares', 'Loaiza', 'Lora', 'Lozano', 'Lucero', 
            'Luque', 'Macías', 'Maldonado', 'Manchego', 'Marín', 'Mariscal', 
            'Márquez', 'Marrufo', 'Martí', 'Méndez', 'Meneses', 'Meza', 'Millan', 
            'Monroy', 'Montejo', 'Montero', 'Montiel', 'Mora', 'Moscoso', 'Moya', 
            'Murillo', 'Nava', 'Nieto', 'Niño', 'Nogales', 'Oliva', 'Olivares', 
            'Ordoñez', 'Ortega', 'Osorio', 'Otero', 'Padilla', 'Palacios', 
            'Palma', 'Paniagua', 'Pantoja', 'Parada', 'Pardo', 'Pastor', 'Patiño', 
            'Paul', 'Pedraza', 'Peredo', 'Pereira', 'Pérez de Acha', 'Pérez', 
            'Pinilla', 'Pizarro', 'Poma', 'Ponce', 'Pozo', 'Prado', 'Prudencio', 
            'Quezada', 'Quintana', 'Quiroga', 'Real', 'Reque', 'Ribera', 'Rico', 
            'Rincón', 'Río', 'Rivas', 'Roldán', 'Romo', 'Rosales', 'Rubio', 
            'Ruiz', 'Saavedra', 'Sagárnaga', 'Salas', 'Salinas', 'Sandi', 
            'Sanjinés', 'Santa Cruz', 'Santana', 'Santiago', 'Santos', 'Saravia', 
            'Sarmiento', 'Sejas', 'Serrano', 'Sierra', 'Solano', 'Solorzano', 
            'Soruco', 'Sotelo', 'Taboada', 'Tamayo', 'Tejerina', 'Terrazas', 
            'Toledo', 'Toribio', 'Torrejón', 'Tórrez', 'Trujillo', 'Ugalde', 
            'Urdininea', 'Uría', 'Uriona', 'Urizar', 'Urquidi', 'Valdivia', 
            'Valenzuela', 'Valverde', 'Vásquez', 'Vega', 'Velarde', 'Velasco', 
            'Vildoso', 'Villagómez', 'Villanueva', 'Villar', 'Villegas', 'Yáñez', 
            'Yucra', 'Yujra', 'Zacarías', 'Zegada', 'Zúñiga'
        ];
        $colegios = [
            'Colegio Nacional Florida',
            'Colegio Nacional Germán Busch',
            'Colegio Nacional Sebastián Pagador',
            'Colegio Nacional Simón Bolívar',
            'Colegio Nacional Mariano Moreno',
            'Colegio Nacional José Manuel Baca',
            'Colegio Nacional Juan XXIII',
            'Colegio Nacional 6 de Agosto',
            'Colegio Nacional 24 de Septiembre',
            'Colegio Nacional Bolívar',
            'Colegio Nacional Sucre',
            'Colegio Nacional San Martín',
            'Colegio Nacional Julio César Valdez',
            'Colegio Nacional Los Pinos',
            'Colegio Nacional Japón',
            'Colegio Nacional Venezuela',
            'Colegio Nacional Uruguay',
            'Colegio Nacional Paraguay',
            'Colegio Nacional México',
            'Colegio Nacional Cuba',
            'Colegio Don Bosco',
            'Colegio La Salle',
            'Colegio Saint George',
            'Colegio Marista',
            'Colegio Fe y Alegría',
            'Colegio Bautista',
            'Colegio Uboldi',
            'Colegio Británico',
            'Colegio Alemán',
            'Colegio Francés',
            'Colegio Americano',
            'Colegio Santa Ana',
            'Colegio Santa Úrsula',
            'Colegio Santo Tomás',
            'Colegio San Agustín',
            'Colegio San Ignacio',
            'Colegio San Francisco',
            'Colegio San José',
            'Colegio San Patricio',
            'Colegio San Felipe Neri',
            'Colegio San Alberto Magno',
            'Colegio San Lucas',
            'Colegio San Luis',
            'Colegio San Rafael',
            'Colegio San Simón',
            'Colegio San Roque',
            'Colegio San Miguel',
            'Colegio Jesús María',
            'Colegio María Auxiliadora',
            'Colegio Sagrado Corazón'
        ];
        $ciudades = [

            'Santa Cruz de la Sierra', 'Montero', 'Warnes', 'Cotoca', 'La Guardia', 
            'El Torno', 'Yapacaní', 'San Ignacio de Velasco', 'San José de Chiquitos', 
            'Roboré', 'Puerto Suárez', 'Camatindi', 'Charagua', 'Cabezas', 'Pailón', 
            'San Julián', 'Cuatro Cañadas', 'San Pedro', 'Comarapa', 'Samaipata', 
            'Vallegrande', 'Postrer Valle', 'Mairana', 'Los Negros', 'Buena Vista',
            
            'La Paz', 'El Alto', 'Viacha', 'Coroico', 'Caranavi', 'Chulumani', 
            'Patacamaya', 'Lahuachaca', 'Guanay', 'Mapiri', 'Apolo', 'Ixiamas', 
            'Puerto Acosta', 'Achacachi', 'Huatajata', 'Copacabana', 'Desaguadero', 
            'Laja', 'Sica Sica', 'Eucaliptus', 'Colquiri', 'Quime', 'Yanacachi',

            'Cochabamba', 'Sacaba', 'Quillacollo', 'Colcapirhua', 'Vinto', 'Tiquipaya', 
            'Sipe Sipe', 'Aiquile', 'Mizque', 'Cliza', 'Punata', 'Arque', 'Capinota', 
            'Epizana', 'Tarata', 'Villa Rivero', 'Puerto Villarroel', 'Shinahota', 
            'Chimoré', 'Villa Tunari', 'Totora', 'Pocona', 'Comarapa', 'Pasorapa',
            
            'Potosí', 'Llallagua', 'Villa Martín', 'Uyuni', 'Colquechaca', 'Uncia', 
            'Tupiza', 'Villazón', 'Atocha', 'Santa Bárbara', 'Betanzos', 'Puna', 
            'Cotagaita', 'Tinguipaya', 'Tomave', 'Acasio', 'Caiza D', 'San Pedro de Quemes',
            
            'Sucre', 'Yotala', 'Monteagudo', 'Padilla', 'Camargo', 'Villa Serrano', 
            'Tarabuco', 'Yamparáez', 'Zudáñez', 'Presto', 'Mojocoya', 'Huacareta', 
            'Incahuasi', 'Villa Vaca Guzmán', 'Icla', 'Poroma',

            'Tarija', 'Yacuiba', 'Villamontes', 'Bermejo', 'Entre Ríos', 'Caraparí', 
            'Uriondo', 'Padcaya', 'San Lorenzo', 'El Puente', 'San José de Pocitos',
            
            'Oruro', 'Huanuni', 'Machacamarca', 'Eucaliptus', 'Caracollo', 'Toledo', 
            'Poopó', 'Challapata', 'Litoral', 'Salinas de Garcí Mendoza', 'Curahuara de Carangas',
            
            'Trinidad', 'Riberalta', 'Guayaramerín', 'San Borja', 'Santa Ana del Yacuma', 
            'Rurrenabaque', 'Reyes', 'San Ignacio de Moxos', 'Loreto', 'San Ramón', 
            'Puerto Siles', 'Exaltación', 'Puerto América',
            
            'Cobija', 'Puerto Rico', 'Porvenir', 'San Lorenzo', 'Filadelfia', 
            'Bella Vista', 'Santa Rosa del Abuná', 'Nueva Esperanza', 'Villa Bush', 
            'Santos Mercado', 'El Sena', 'San Miguelito', 'Pto. Gonzalo Moreno'
        ];

        $passwordHashed = Hash::make('postulante123');
        $carreras = DB::table('carrera')->pluck('id')->toArray();
        if (count($carreras) < 2) {
            // Asegurar carreras mínimas
            $carreras[] = DB::table('carrera')->insertGetId(['nombre' => 'Ingeniería en Sistemas', 'cupo_maximo' => 200], 'id');
            $carreras[] = DB::table('carrera')->insertGetId(['nombre' => 'Ingeniería Informática', 'cupo_maximo' => 150], 'id');
        }

        $gestionesASeeder = ['2-2024', '1-2025', '2-2025'];

        echo "Comenzando la siembra de estudiantes (600 por gestión)..." . PHP_EOL;

        DB::transaction(function () use ($gestionesASeeder, $gestionIds, $carreras, $turnoIds, $nombresMasculinos, $nombresFemeninos, $apellidos, $colegios, $ciudades, $passwordHashed, $getOrCreateAvailableGroup) {
            foreach ($gestionesASeeder as $gKey) {
                $gestionId = $gestionIds[$gKey];
                echo "Procesando gestión {$gKey} (ID: {$gestionId})..." . PHP_EOL;

                for ($i = 1; $i <= 600; $i++) {
                    $esVarón = (rand(0, 1) === 0);
                    $nombre = $esVarón ? $nombresMasculinos[array_rand($nombresMasculinos)] : $nombresFemeninos[array_rand($nombresFemeninos)];
                    $apellido1 = $apellidos[array_rand($apellidos)];
                    $apellido2 = $apellidos[array_rand($apellidos)];
                    $apellidosCompletos = "{$apellido1} {$apellido2}";
                    
                    // CIs únicos por estudiante y gestión
                    $ci = rand(5000000, 9999999) . '-' . $gestionId . '-' . $i;
                    $sexo = $esVarón ? 'M' : 'F';
                    $correo = strtolower($nombre) . '.' . strtolower($apellido1) . rand(100, 999) . '_' . $gestionId . '_' . $i . '@postulante.cup.edu';

                    // 1. Crear el usuario
                    $uId = DB::table('usuario')->insertGetId([
                        'nombre' => $nombre,
                        'apellidos' => $apellidosCompletos,
                        'ci' => $ci,
                        'contrasena' => $passwordHashed,
                        'fechanac' => '2006-' . sprintf('%02d', rand(1, 12)) . '-' . sprintf('%02d', rand(1, 28)),
                        'sexo' => $sexo,
                        'direccion' => 'Barrio Lindo, C/' . rand(1, 20),
                        'telefono' => '7' . rand(1000000, 9999999),
                        'rol' => 'postulante',
                        'correo' => $correo,
                        'fecha' => date('Y-m-d')
                    ], 'id_usuario');

                    // Elegir turno preferido y grupo correspondientes
                    $turnoElegido = array_rand($turnoIds); // 'Mañana', 'Tarde' o 'Noche'
                    $idTurnoPreferido = $turnoIds[$turnoElegido];

                    $idGrupoElegido = $getOrCreateAvailableGroup($turnoElegido);

                    $idCarrera1 = $carreras[array_rand($carreras)];
                    $idCarrera2 = $carreras[array_rand($carreras)];
                    while ($idCarrera1 === $idCarrera2) {
                        $idCarrera2 = $carreras[array_rand($carreras)];
                    }

                    // 2. Crear el postulante con requisitos ya aprobados para poder asignarles el grupo
                    DB::table('postulante')->insert([
                        'id_postulante' => $uId,
                        'colegio_procedencia' => $colegios[array_rand($colegios)],
                        'ciudad' => $ciudades[array_rand($ciudades)],
                        'titulo_bachiller' => true,
                        'libreta_de_ultimo_anio' => true,
                        'archivo_titulo_bachiller' => 'requisitos/' . $uId . '/titulo.pdf',
                        'archivo_libreta' => 'requisitos/' . $uId . '/libreta.pdf',
                        'id_carrera_primera' => $idCarrera1,
                        'id_carrera_segunda' => $idCarrera2,
                        'id_grupo' => $idGrupoElegido,
                        'id_gestion' => $gestionId,
                        'id_turno_preferido' => $idTurnoPreferido
                    ]);

                    // 3. Crear el pago aprobado correspondiente
                    DB::table('pago')->insert([
                        'id_postulante' => $uId,
                        'monto' => 700.00,
                        'moneda' => 'BOB',
                        'paypal_order_id' => 'PAYID-' . strtoupper(Str::random(16)),
                        'estado' => 'APROBADO',
                        'fecha_pago' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
                    ]);

                    // 4. Crear notas para las 4 materias (las notas finales se calcularán por trigger de Postgres)
                    for ($m = 1; $m <= 4; $m++) {
                        DB::table('notas')->insert([
                            'id_postulante' => $uId,
                            'id_materia' => $m,
                            'examen1' => rand(30, 100),
                            'examen2' => rand(30, 100),
                            'examen3' => rand(30, 100),
                        ]);
                    }
                }
            }
        });

        echo "Estudiantes y calificaciones sembradas con éxito." . PHP_EOL;

        // 10. Ejecutar el procedimiento de admisión final para cada gestión
        echo "Ejecutando procedimiento de admisión para cada gestión..." . PHP_EOL;
        foreach ($gestionesASeeder as $gKey) {
            $gestionId = $gestionIds[$gKey];
            DB::statement("CALL pr_procesar_admision_cup(?)", [$gestionId]);
            echo "Admisión procesada para la gestión {$gKey} (ID: {$gestionId})." . PHP_EOL;
        }

        echo "Siembra de datos completamente terminada!" . PHP_EOL;
    }
}
