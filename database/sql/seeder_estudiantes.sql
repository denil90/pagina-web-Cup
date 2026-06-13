-- Asegurar que la función trigger fn_verificar_cupo_grupo esté actualizada y filtre por gestión (permitiendo reutilizar los grupos)
CREATE OR REPLACE FUNCTION fn_verificar_cupo_grupo()
RETURNS TRIGGER AS $$
DECLARE
    v_capacidad_maxima INT;
    v_actuales_inscritos INT;
BEGIN
    IF NEW.id_grupo IS NULL THEN
        RETURN NEW;
    END IF;

    SELECT capacidad_maxima INTO v_capacidad_maxima
    FROM grupo WHERE id_grupo = NEW.id_grupo;

    -- Contar inscritos filtrando por la gestión del postulante
    SELECT COUNT(*) INTO v_actuales_inscritos
    FROM postulante 
    WHERE id_grupo = NEW.id_grupo 
      AND id_gestion = NEW.id_gestion;

    IF v_actuales_inscritos >= v_capacidad_maxima THEN
        RAISE EXCEPTION 'Error: El grupo con ID % ya alcanzó su capacidad máxima de % estudiantes para la gestión %.',
            NEW.id_grupo, v_capacidad_maxima, NEW.id_gestion;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DO $$
DECLARE
    -- Arrays de datos para generación aleatoria
    nombres_m TEXT[] := ARRAY['Alejandro', 'Andres', 'Carlos', 'Cristian', 'Daniel', 'David', 'Diego', 'Eduardo', 'Fernando', 'Gabriel', 'Guillermo', 'Javier', 'Jorge', 'Jose', 'Juan', 'Julio', 'Luis', 'Manuel', 'Mario', 'Mauricio', 'Miguel', 'Oscar', 'Pablo', 'Pedro', 'Ricardo', 'Roberto', 'Rodrigo', 'Santiago', 'Sebastian', 'Victor', 'Walter', 'Adrian', 'Alan', 'Alberto', 'Alvaro', 'Antonio', 'Armando', 'Arturo', 'Benjamin', 'Braulio', 'Bruno', 'Camilo', 'Cesar', 'Claudio', 'Clemente', 'Cristobal', 'Damian', 'Dario', 'Denis', 'Edgar', 'Edison', 'Edwin', 'Elias', 'Emiliano', 'Emilio', 'Enrique', 'Eric', 'Ernesto', 'Esteban', 'Ezequiel', 'Fabian', 'Federico', 'Felipe', 'Felix', 'Francisco', 'Gerardo', 'Gerson', 'Gonzalo', 'Gregorio', 'Gustavo', 'Hector', 'Hernan', 'Homero', 'Horacio', 'Hugo', 'Ignacio', 'Ivan', 'Jesus', 'Joaquin', 'Jonathan', 'Jordan', 'Josue', 'Julian', 'Leonardo', 'Lorenzo', 'Lucas', 'Marcelo', 'Marco', 'Marcos', 'Martin', 'Mateo', 'Matias', 'Moises', 'Nicolas', 'Noe', 'Oliver', 'Orlando', 'Patricio', 'Rafael', 'Ramon', 'Raul', 'Rene', 'Ruben', 'Samuel', 'Saul', 'Sergio', 'Tomas', 'Valentin', 'Vicente'];
    nombres_f TEXT[] := ARRAY['Alejandra', 'Andrea', 'Camila', 'Carolina', 'Claudia', 'Daniela', 'Diana', 'Elizabeth', 'Estefania', 'Gabriela', 'Isabel', 'Jessica', 'Laura', 'Leticia', 'Lorena', 'Lucia', 'Maria', 'Natalia', 'Patricia', 'Paola', 'Raquel', 'Sara', 'Sofia', 'Valeria', 'Vanessa', 'Veronica', 'Adriana', 'Alba', 'Alexandra', 'Alicia', 'Alondra', 'Ana', 'Angelica', 'Antonia', 'Ariana', 'Beatriz', 'Brenda', 'Carla', 'Cecilia', 'Cindy', 'Cristina', 'Debora', 'Denisse', 'Dolores', 'Elena', 'Elisa', 'Eliana', 'Emilia', 'Erika', 'Esperanza', 'Eva', 'Fabiana', 'Fernanda', 'Francisca', 'Geraldine', 'Gloria', 'Graciela', 'Guadalupe', 'Helen', 'Iliana', 'Irene', 'Irina', 'Jackeline', 'Jacqueline', 'Jazmin', 'Jennifer', 'Jimena', 'Johana', 'Josefina', 'Julieta', 'Karen', 'Katherine', 'Kenia', 'Liliana', 'Lina', 'Lissa', 'Lizeth', 'Lourdes', 'Luisa', 'Magaly', 'Maira', 'Marcela', 'Margarita', 'Mariana', 'Marisol', 'Marta', 'Melanie', 'Melissa', 'Mercedes', 'Micaela', 'Monica', 'Nadia', 'Nancy', 'Nicole', 'Noelia', 'Olivia', 'Pamela', 'Paula', 'Rocio', 'Rosa', 'Rosario', 'Rosaura', 'Ruth', 'Sabrina', 'Samanta', 'Silvia', 'Susana', 'Tamara', 'Tania', 'Teresa', 'Valentina', 'Viviana', 'Yesica', 'Zulma'];
    apellidos TEXT[] := ARRAY['Quispe', 'Mamani', 'Condori', 'Choque', 'Flores', 'Rodriguez', 'Vargas', 'Guzman', 'Rojas', 'Ortiz', 'Sanchez', 'Chavez', 'Ramos', 'Torrez', 'Gutierrez', 'Castro', 'Romero', 'Fernandez', 'Suarez', 'Justiniano', 'Cuellar', 'Aguilera', 'Vaca', 'Pinto', 'Mercado', 'Roca', 'Hurtado', 'Soliz', 'Mendoza', 'Peralta', 'Montano', 'Llanos', 'Arias', 'Barrios', 'Cabrera', 'Calderon', 'Camacho', 'Cano', 'Cardenas', 'Cortez', 'Cruz', 'Delgado', 'Diaz', 'Duran', 'Escobar', 'Espinoza', 'Figueroa', 'Fuentes', 'Galindo', 'Garcia', 'Gomez', 'Gonzales', 'Guarachi', 'Herrera', 'Ibanez', 'Jimenez', 'Lara', 'Lopez', 'Luna', 'Martinez', 'Medina', 'Miranda', 'Molina', 'Morales', 'Moreno', 'Munoz', 'Navarro', 'Nunez', 'Orellana', 'Paredes', 'Paz', 'Pena', 'Perez', 'Ramirez', 'Reyes', 'Rios', 'Rivera', 'Robles', 'Saenz', 'Salazar', 'Silva', 'Soria', 'Sosa', 'Tapia', 'Tarqui', 'Torrico', 'Ulloa', 'Urquizo', 'Valdez', 'Valencia', 'Vera', 'Villalobos', 'Villarroel', 'Zabala', 'Zambrana', 'Zapata'];
    colegios TEXT[] := ARRAY['Colegio Nacional Florida', 'Colegio Nacional German Busch', 'Colegio Nacional Sebastian Pagador', 'Colegio Nacional Simon Bolivar', 'Colegio Nacional Mariano Moreno', 'Colegio Nacional Jose Manuel Baca', 'Colegio Nacional Juan XXIII', 'Colegio Nacional 6 de Agosto', 'Colegio Nacional 24 de Septiembre', 'Colegio Nacional Bolivar', 'Colegio Don Bosco', 'Colegio La Salle', 'Colegio Saint George', 'Colegio Marista', 'Colegio Fe y Alegria', 'Colegio Baptist', 'Colegio Uboldi', 'Colegio Santa Ana', 'Colegio San Agustin', 'Colegio San Ignacio', 'Colegio Sagrado Corazon'];
    ciudades TEXT[] := ARRAY['Santa Cruz de la Sierra', 'Montero', 'Warnes', 'Cotoca', 'La Guardia', 'El Torno', 'Yapacani', 'San Ignacio de Velasco', 'San Jose de Chiquitos', 'Robore', 'La Paz', 'El Alto', 'Viacha', 'Coroico', 'Cochabamba', 'Sacaba', 'Quillacollo', 'Colcapirhua', 'Potosi', 'Uyuni', 'Sucre', 'Tarija', 'Yacuiba', 'Bermejo', 'Oruro', 'Trinidad', 'Cobija'];
    turnos TEXT[] := ARRAY['Mañana', 'Tarde', 'Noche'];
    carreras INT[];

    -- Variables de control y auxiliares
    v_gestion_id INT;
    v_turno_id INT;
    v_turno_nombre TEXT;
    v_aula_id INT;
    v_aula_start INT;
    v_grupo_id INT;
    v_grupo_nombre TEXT;
    v_prefix CHAR(1);
    v_max_num INT;
    v_next_num INT;
    
    -- Variables para creación de docentes/horarios
    v_docente_id INT;
    v_docente_num INT;
    v_m_id INT;
    v_h_block INT;
    v_horario_id INT;
    v_temp_count INT;
    v_load_count INT;

    -- Variables de generación de estudiantes
    es_varon BOOLEAN;
    v_nombre TEXT;
    v_apellido1 TEXT;
    v_apellido2 TEXT;
    v_apellidos_completos TEXT;
    v_ci TEXT;
    v_sexo CHAR(1);
    v_correo TEXT;
    v_user_id INT;
    v_carrera1 INT;
    v_carrera2 INT;
    v_pago_id TEXT;
    
    -- Cursores / records
    r_docente RECORD;
    r_gestion RECORD;
    
    -- Contador
    i INT;
    j INT;
    
    -- Gestiones a sembrar
    gestiones_info RECORD;
BEGIN
    -- 1. Limpieza de postulantes sembrados previamente
    -- Buscamos usuarios cuyo correo termine en '@postulante.cup.edu'
    CREATE TEMP TABLE IF NOT EXISTS tmp_seeded_users AS
    SELECT id_usuario FROM usuario WHERE correo LIKE '%@postulante.cup.edu';
    
    DELETE FROM admision_final WHERE id_postulante IN (SELECT id_usuario FROM tmp_seeded_users);
    DELETE FROM notas WHERE id_postulante IN (SELECT id_usuario FROM tmp_seeded_users);
    DELETE FROM pago WHERE id_postulante IN (SELECT id_usuario FROM tmp_seeded_users);
    DELETE FROM postulante WHERE id_postulante IN (SELECT id_usuario FROM tmp_seeded_users);
    DELETE FROM usuario WHERE id_usuario IN (SELECT id_usuario FROM tmp_seeded_users);
    
    DROP TABLE IF EXISTS tmp_seeded_users;

    -- 2. Asegurar gestiones en la BD
    -- Gestión 2-2024
    IF NOT EXISTS (SELECT 1 FROM gestion WHERE semestre = '2' AND anio = 2024) THEN
        INSERT INTO gestion (semestre, anio) VALUES ('2', 2024);
    END IF;
    -- Gestión 1-2025
    IF NOT EXISTS (SELECT 1 FROM gestion WHERE semestre = '1' AND anio = 2025) THEN
        INSERT INTO gestion (semestre, anio) VALUES ('1', 2025);
    END IF;
    -- Gestión 2-2025
    IF NOT EXISTS (SELECT 1 FROM gestion WHERE semestre = '2' AND anio = 2025) THEN
        INSERT INTO gestion (semestre, anio) VALUES ('2', 2025);
    END IF;

    -- 3. Asegurar turnos en la BD
    FOREACH v_turno_nombre IN ARRAY turnos LOOP
        IF NOT EXISTS (SELECT 1 FROM turno WHERE nombre = v_turno_nombre) THEN
            INSERT INTO turno (nombre) VALUES (v_turno_nombre);
        END IF;
    END LOOP;

    -- 4. Asegurar aulas necesarias (13..17, 23..27, 33..37)
    FOR i IN 13..17 LOOP
        IF NOT EXISTS (SELECT 1 FROM aula WHERE id_aula = i) THEN
            INSERT INTO aula (id_aula, nombre, edificio, capacidad) VALUES (i, 'Aula ' || i, 'Edificio Adicional', 70);
        END IF;
    END LOOP;
    FOR i IN 23..27 LOOP
        IF NOT EXISTS (SELECT 1 FROM aula WHERE id_aula = i) THEN
            INSERT INTO aula (id_aula, nombre, edificio, capacidad) VALUES (i, 'Aula ' || i, 'Edificio Adicional', 70);
        END IF;
    END LOOP;
    FOR i IN 33..37 LOOP
        IF NOT EXISTS (SELECT 1 FROM aula WHERE id_aula = i) THEN
            INSERT INTO aula (id_aula, nombre, edificio, capacidad) VALUES (i, 'Aula ' || i, 'Edificio Adicional', 70);
        END IF;
    END LOOP;

    -- Asegurar al menos dos aulas por defecto por si acaso
    IF NOT EXISTS (SELECT 1 FROM aula WHERE id_aula = 1) THEN
        INSERT INTO aula (id_aula, nombre, edificio, capacidad) VALUES (1, 'Aula 101', 'Edificio A', 70);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM aula WHERE id_aula = 2) THEN
        INSERT INTO aula (id_aula, nombre, edificio, capacidad) VALUES (2, 'Aula 102', 'Edificio A', 70);
    END IF;

    -- 5. Asegurar grupos base (M1, M2, T1, T2, N1, N2)
    -- Asignando aulas y turnos aleatorios de los existentes
    FOREACH v_grupo_nombre IN ARRAY ARRAY['M1', 'M2', 'T1', 'T2', 'N1', 'N2'] LOOP
        IF strpos(v_grupo_nombre, 'M') = 1 THEN
            SELECT id_turno INTO v_turno_id FROM turno WHERE nombre = 'Mañana';
        ELSIF strpos(v_grupo_nombre, 'T') = 1 THEN
            SELECT id_turno INTO v_turno_id FROM turno WHERE nombre = 'Tarde';
        ELSE
            SELECT id_turno INTO v_turno_id FROM turno WHERE nombre = 'Noche';
        END IF;
        
        IF NOT EXISTS (SELECT 1 FROM grupo WHERE nombre = v_grupo_nombre) THEN
            INSERT INTO grupo (nombre, capacidad_maxima, id_aula, id_turno)
            VALUES (v_grupo_nombre, 70, (SELECT id_aula FROM aula ORDER BY random() LIMIT 1), v_turno_id);
        ELSE
            UPDATE grupo SET capacidad_maxima = 70 WHERE nombre = v_grupo_nombre;
        END IF;
    END LOOP;

    -- 6. Asegurar 8 docentes mínimos en la BD
    SELECT ARRAY(SELECT id_docente FROM docente) INTO carreras; -- Usamos carreras temporalmente como array de docentes
    v_docente_num := array_length(carreras, 1);
    IF v_docente_num IS NULL THEN v_docente_num := 0; END IF;
    
    WHILE v_docente_num < 8 LOOP
        v_docente_num := v_docente_num + 1;
        INSERT INTO usuario (nombre, apellidos, ci, contrasena, fechanac, sexo, direccion, telefono, rol, correo)
        VALUES (
            'Docente' || v_docente_num,
            'ApellidoDocente' || v_docente_num,
            '4000' || floor(random() * 899 + 100)::TEXT || v_docente_num,
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- contrasena: docente123
            '1982-05-14',
            CASE WHEN v_docente_num % 2 = 0 THEN 'M' ELSE 'F' END,
            'Av. Busch S/N',
            '789456' || v_docente_num,
            'docente',
            'docente' || v_docente_num || '_' || substring(md5(random()::text) from 1 for 3) || '@cup.edu'
        ) RETURNING id_usuario INTO v_docente_id;
        
        INSERT INTO docente (id_docente, titulo_profesional, estado)
        VALUES (v_docente_id, 'Licenciado en Ciencias', 'ACTIVO');
    END LOOP;

    -- 7. Asegurar las materias
    -- Computación (1), Matemáticas (2), Inglés (3), Física (4)
    IF NOT EXISTS (SELECT 1 FROM materia WHERE id_materia = 1) THEN
        INSERT INTO materia (id_materia, nombre, porcentaje_examen1, porcentaje_examen2, porcentaje_examen3)
        VALUES (1, 'Computación', 33.33, 33.33, 33.34);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM materia WHERE id_materia = 2) THEN
        INSERT INTO materia (id_materia, nombre, porcentaje_examen1, porcentaje_examen2, porcentaje_examen3)
        VALUES (2, 'Matemáticas', 33.33, 33.33, 33.34);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM materia WHERE id_materia = 3) THEN
        INSERT INTO materia (id_materia, nombre, porcentaje_examen1, porcentaje_examen2, porcentaje_examen3)
        VALUES (3, 'Inglés', 33.33, 33.33, 33.34);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM materia WHERE id_materia = 4) THEN
        INSERT INTO materia (id_materia, nombre, porcentaje_examen1, porcentaje_examen2, porcentaje_examen3)
        VALUES (4, 'Física', 33.33, 33.33, 33.34);
    END IF;

    -- 8. Asegurar los horarios base de lunes
    -- Mañana
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '07:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '07:00:00', '08:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '08:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '08:00:00', '09:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '09:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '09:00:00', '10:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '10:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '10:00:00', '11:00:00');
    END IF;
    -- Tarde
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '14:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '14:00:00', '15:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '15:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '15:00:00', '16:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '16:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '16:00:00', '17:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '17:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '17:00:00', '18:00:00');
    END IF;
    -- Noche
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '19:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '19:00:00', '20:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '20:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '20:00:00', '21:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '21:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '21:00:00', '22:00:00');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM horario WHERE dia = 'Lunes' AND hora_inicio = '22:00:00'::TIME) THEN
        INSERT INTO horario (dia, hora_inicio, hora_final) VALUES ('Lunes', '22:00:00', '23:00:00');
    END IF;

    -- Asignación docente_grupo inicial para M1, M2, T1, T2, N1, N2 si no tienen
    FOREACH v_grupo_nombre IN ARRAY ARRAY['M1', 'M2', 'T1', 'T2', 'N1', 'N2'] LOOP
        SELECT id_grupo INTO v_grupo_id FROM grupo WHERE nombre = v_grupo_nombre;
        IF NOT EXISTS (SELECT 1 FROM docente_grupo WHERE id_grupo = v_grupo_id) THEN
            IF strpos(v_grupo_nombre, 'M') = 1 THEN v_turno_nombre := 'Mañana';
            ELSIF strpos(v_grupo_nombre, 'T') = 1 THEN v_turno_nombre := 'Tarde';
            ELSE v_turno_nombre := 'Noche';
            END IF;
            
            -- Asignar docentes a las 4 materias en sus 4 horarios
            FOR v_h_block IN 0..3 LOOP
                -- Determinar materia
                IF v_h_block = 0 THEN v_m_id := 3; -- Inglés
                ELSIF v_h_block = 1 THEN v_m_id := 1; -- Computación
                ELSIF v_h_block = 2 THEN v_m_id := 4; -- Física
                ELSE v_m_id := 2; -- Matemáticas
                END IF;

                -- Obtener horario
                SELECT id_horario INTO v_horario_id FROM horario
                WHERE dia = 'Lunes' AND hora_inicio = (
                    CASE 
                        WHEN v_turno_nombre = 'Mañana' THEN (CASE WHEN v_h_block = 0 THEN '07:00:00' WHEN v_h_block = 1 THEN '08:00:00' WHEN v_h_block = 2 THEN '09:00:00' ELSE '10:00:00' END)
                        WHEN v_turno_nombre = 'Tarde' THEN (CASE WHEN v_h_block = 0 THEN '14:00:00' WHEN v_h_block = 1 THEN '15:00:00' WHEN v_h_block = 2 THEN '16:00:00' ELSE '17:00:00' END)
                        ELSE (CASE WHEN v_h_block = 0 THEN '19:00:00' WHEN v_h_block = 1 THEN '20:00:00' WHEN v_h_block = 2 THEN '21:00:00' ELSE '22:00:00' END)
                    END
                )::TIME;

                -- Docente aleatorio
                SELECT id_docente INTO v_docente_id FROM docente ORDER BY random() LIMIT 1;

                INSERT INTO docente_grupo (id_docente, id_grupo, id_materia, id_horario)
                VALUES (v_docente_id, v_grupo_id, v_m_id, v_horario_id);
            END LOOP;
        END IF;
    END LOOP;

    -- Obtener carreras de la BD
    SELECT ARRAY(SELECT id FROM carrera) INTO carreras;
    -- Si no hay suficientes carreras, asegurar dos
    IF array_length(carreras, 1) IS NULL OR array_length(carreras, 1) < 2 THEN
        IF NOT EXISTS (SELECT 1 FROM carrera WHERE nombre = 'Ingeniería en Sistemas') THEN
            INSERT INTO carrera (nombre, descripcion, cupo_maximo) VALUES ('Ingeniería en Sistemas', 'Sistemas', 200);
        END IF;
        IF NOT EXISTS (SELECT 1 FROM carrera WHERE nombre = 'Ingeniería Informática') THEN
            INSERT INTO carrera (nombre, descripcion, cupo_maximo) VALUES ('Ingeniería Informática', 'Informática', 150);
        END IF;
        SELECT ARRAY(SELECT id FROM carrera) INTO carreras;
    END IF;

    -- 10. Bucle de Siembra de Postulantes (600 por gestión)
    FOR r_gestion IN (
        SELECT id_gestion, semestre, anio 
        FROM gestion 
        WHERE (semestre = '2' AND anio = 2024) 
           OR (semestre = '1' AND anio = 2025) 
           OR (semestre = '2' AND anio = 2025)
    ) LOOP
        RAISE NOTICE 'Procesando gestion %-% (ID: %)...', r_gestion.semestre, r_gestion.anio, r_gestion.id_gestion;
        
        FOR i IN 1..600 LOOP
            -- Generación de datos aleatorios
            es_varon := (random() > 0.5);
            IF es_varon THEN
                v_nombre := nombres_m[floor(random() * array_length(nombres_m, 1)) + 1];
                v_sexo := 'M';
            ELSE
                v_nombre := nombres_f[floor(random() * array_length(nombres_f, 1)) + 1];
                v_sexo := 'F';
            END IF;
            
            v_apellido1 := apellidos[floor(random() * array_length(apellidos, 1)) + 1];
            v_apellido2 := apellidos[floor(random() * array_length(apellidos, 1)) + 1];
            v_apellidos_completos := v_apellido1 || ' ' || v_apellido2;
            
            v_ci := (5000000 + floor(random() * 4999999))::TEXT || '-' || r_gestion.id_gestion || '-' || i;
            v_correo := lower(v_nombre) || '.' || lower(v_apellido1) || floor(random()*899+100)::TEXT || '_' || r_gestion.id_gestion || '_' || i || '@postulante.cup.edu';

            -- 1. Insertar en tabla usuario
            INSERT INTO usuario (nombre, apellidos, ci, contrasena, fechanac, sexo, direccion, telefono, rol, correo, fecha)
            VALUES (
                v_nombre,
                v_apellidos_completos,
                v_ci,
                '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- contrasena: postulante123
                ('2006-' || lpad(floor(random()*11+1)::TEXT, 2, '0') || '-' || lpad(floor(random()*27+1)::TEXT, 2, '0'))::DATE,
                v_sexo,
                'Barrio Lindo, C/' || floor(random()*19+1),
                '7' || floor(random()*8999999+1000000)::TEXT,
                'postulante',
                v_correo,
                CURRENT_DATE
            ) RETURNING id_usuario INTO v_user_id;

            -- 2. Elegir turno preferido y grupo correspondientes
            v_turno_nombre := turnos[floor(random() * 3) + 1];
            SELECT id_turno INTO v_turno_id FROM turno WHERE nombre = v_turno_nombre;
            
            -- Obtener o crear grupo con espacio en ese turno para esta gestión
            v_grupo_id := NULL;
            SELECT g.id_grupo INTO v_grupo_id
            FROM grupo g
            WHERE g.id_turno = v_turno_id
              AND (SELECT COUNT(*) FROM postulante p WHERE p.id_grupo = g.id_grupo AND p.id_gestion = r_gestion.id_gestion) < 70
            ORDER BY g.id_grupo
            LIMIT 1;

            -- Si no hay grupo con espacio, crear uno nuevo
            IF v_grupo_id IS NULL THEN
                IF v_turno_nombre = 'Mañana' THEN 
                    v_prefix := 'M'; v_aula_start := 13;
                ELSIF v_turno_nombre = 'Tarde' THEN 
                    v_prefix := 'T'; v_aula_start := 23;
                ELSE 
                    v_prefix := 'N'; v_aula_start := 33;
                END IF;

                -- Calcular número correlativo del grupo
                SELECT COALESCE(MAX(SUBSTRING(nombre FROM 2)::INT), 2) INTO v_max_num
                FROM grupo
                WHERE nombre LIKE v_prefix || '%';
                v_next_num := v_max_num + 1;
                v_grupo_nombre := v_prefix || v_next_num;

                -- Aula correlativa
                v_aula_id := v_aula_start + ((v_next_num - 3) % 5);
                IF NOT EXISTS (SELECT 1 FROM aula WHERE id_aula = v_aula_id) THEN
                    SELECT id_aula INTO v_aula_id FROM aula ORDER BY random() LIMIT 1;
                END IF;

                -- Insertar nuevo grupo
                INSERT INTO grupo (nombre, capacidad_maxima, id_aula, id_turno)
                VALUES (v_grupo_nombre, 70, v_aula_id, v_turno_id)
                RETURNING id_grupo INTO v_grupo_id;

                -- Asignar docentes a este nuevo grupo para sus materias
                FOR v_h_block IN 0..3 LOOP
                    IF v_h_block = 0 THEN v_m_id := 3; -- Inglés
                    ELSIF v_h_block = 1 THEN v_m_id := 1; -- Computación
                    ELSIF v_h_block = 2 THEN v_m_id := 4; -- Física
                    ELSE v_m_id := 2; -- Matemáticas
                    END IF;

                    SELECT id_horario INTO v_horario_id FROM horario
                    WHERE dia = 'Lunes' AND hora_inicio = (
                        CASE 
                            WHEN v_turno_nombre = 'Mañana' THEN (CASE WHEN v_h_block = 0 THEN '07:00:00' WHEN v_h_block = 1 THEN '08:00:00' WHEN v_h_block = 2 THEN '09:00:00' ELSE '10:00:00' END)
                            WHEN v_turno_nombre = 'Tarde' THEN (CASE WHEN v_h_block = 0 THEN '14:00:00' WHEN v_h_block = 1 THEN '15:00:00' WHEN v_h_block = 2 THEN '16:00:00' ELSE '17:00:00' END)
                            ELSE (CASE WHEN v_h_block = 0 THEN '19:00:00' WHEN v_h_block = 1 THEN '20:00:00' WHEN v_h_block = 2 THEN '21:00:00' ELSE '22:00:00' END)
                        END
                    )::TIME;

                    -- Buscar docente disponible sin choque y con menos de 3 materias
                    v_docente_id := NULL;
                    FOR r_docente IN (SELECT id_docente FROM docente WHERE estado = 'ACTIVO') LOOP
                        SELECT COUNT(*) INTO v_temp_count
                        FROM docente_grupo dg
                        JOIN grupo g ON dg.id_grupo = g.id_grupo
                        WHERE dg.id_docente = r_docente.id_docente
                          AND g.id_horario = v_horario_id;

                        IF v_temp_count = 0 THEN
                            SELECT COUNT(DISTINCT id_grupo) INTO v_load_count
                            FROM docente_grupo
                            WHERE id_docente = r_docente.id_docente;

                            IF v_load_count < 3 THEN
                                v_docente_id := r_docente.id_docente;
                                EXIT;
                            END IF;
                        END IF;
                    END LOOP;

                    -- Si no hay docentes disponibles, crear uno nuevo
                    IF v_docente_id IS NULL THEN
                        v_docente_num := (SELECT COUNT(*) FROM docente) + 1;
                        INSERT INTO usuario (nombre, apellidos, ci, contrasena, fechanac, sexo, direccion, telefono, rol, correo)
                        VALUES (
                            'Docente' || v_docente_num,
                            'ApellidoDocente' || v_docente_num,
                            '4000' || floor(random() * 899 + 100)::TEXT || v_docente_num,
                            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                            '1982-05-14',
                            'M',
                            'Av. Busch S/N',
                            '789456' || v_docente_num,
                            'docente',
                            'docente' || v_docente_num || '_' || substring(md5(random()::text) from 1 for 3) || '@cup.edu'
                        ) RETURNING id_usuario INTO v_docente_id;

                        INSERT INTO docente (id_docente, titulo_profesional, estado)
                        VALUES (v_docente_id, 'Licenciado en Ciencias', 'ACTIVO');
                    END IF;

                    -- Insertar asignación
                    INSERT INTO docente_grupo (id_docente, id_grupo, id_materia, id_horario)
                    VALUES (v_docente_id, v_grupo_id, v_m_id, v_horario_id);
                END LOOP;
            END IF;

            -- Definir carreras
            v_carrera1 := carreras[floor(random() * array_length(carreras, 1)) + 1];
            v_carrera2 := carreras[floor(random() * array_length(carreras, 1)) + 1];
            WHILE v_carrera1 = v_carrera2 LOOP
                v_carrera2 := carreras[floor(random() * array_length(carreras, 1)) + 1];
            END LOOP;

            -- 3. Insertar postulante
            INSERT INTO postulante (id_postulante, colegio_procedencia, ciudad, titulo_bachiller, libreta_de_ultimo_anio, archivo_titulo_bachiller, archivo_libreta, id_carrera_primera, id_carrera_segunda, id_grupo, id_gestion, id_turno_preferido)
            VALUES (
                v_user_id,
                colegios[floor(random() * array_length(colegios, 1)) + 1],
                ciudades[floor(random() * array_length(ciudades, 1)) + 1],
                TRUE,
                TRUE,
                'requisitos/' || v_user_id || '/titulo.pdf',
                'requisitos/' || v_user_id || '/libreta.pdf',
                v_carrera1,
                v_carrera2,
                v_grupo_id,
                r_gestion.id_gestion,
                v_turno_id
            );

            -- 4. Registrar pago
            v_pago_id := 'PAYID-' || upper(substring(md5(random()::text) from 1 for 16));
            INSERT INTO pago (id_postulante, monto, moneda, paypal_order_id, estado, fecha_pago)
            VALUES (
                v_user_id,
                700.00,
                'BOB',
                v_pago_id,
                'APROBADO',
                (CURRENT_DATE - (floor(random() * 30)::INT || ' days')::INTERVAL)
            );

            -- 5. Registrar notas de materias (1 a 4)
            FOR j IN 1..4 LOOP
                INSERT INTO notas (id_postulante, id_materia, examen1, examen2, examen3)
                VALUES (
                    v_user_id,
                    j,
                    30 + floor(random() * 70),
                    30 + floor(random() * 70),
                    30 + floor(random() * 70)
                );
            END LOOP;
        END LOOP;
        
        -- Ejecutar la admisión final de la gestión respectiva
        CALL pr_procesar_admision_cup(r_gestion.id_gestion);
    END LOOP;
END $$;
