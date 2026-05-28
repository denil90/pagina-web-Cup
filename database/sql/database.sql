-- ==========================================================
-- BASE DE DATOS: Sistema CUP - Curso Preuniversitario FIC
-- ==========================================================

CREATE TABLE usuario (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    ci VARCHAR(20) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    fechanac DATE NOT NULL,
    sexo CHAR(1) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    rol VARCHAR(50) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    fecha DATE DEFAULT CURRENT_DATE
);

CREATE TABLE carrera (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    cupo_maximo INT NOT NULL
);

CREATE TABLE gestion (
    id_gestion SERIAL PRIMARY KEY,
    semestre VARCHAR(20) NOT NULL,
    anio INT NOT NULL
);

CREATE TABLE materia (
    id_materia SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    porcentaje_examen1 NUMERIC(5, 2),
    porcentaje_examen2 NUMERIC(5, 2),
    porcentaje_examen3 NUMERIC(5, 2)
);

CREATE TABLE horario (
    id_horario SERIAL PRIMARY KEY,
    dia VARCHAR(20) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_final TIME NOT NULL
);

CREATE TABLE aula (
    id_aula SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    edificio VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL
);

CREATE TABLE turno (
    id_turno SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE grupo (
    id_grupo SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capacidad_maxima INT NOT NULL,
    id_horario INT REFERENCES horario(id_horario),
    id_aula INT REFERENCES aula(id_aula),
    id_turno INT REFERENCES turno(id_turno)
);

-- ==========================================================
-- TABLAS QUE HEREDAN DE USUARIO
-- ==========================================================

CREATE TABLE administrador (
    id_admin INT PRIMARY KEY REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE docente (
    id_docente INT PRIMARY KEY REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    titulo_profesional VARCHAR(150),
    maestria VARCHAR(150),
    diplomado VARCHAR(150),
    estado VARCHAR(50)
);

CREATE TABLE postulante (
    id_postulante INT PRIMARY KEY REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    colegio_procedencia VARCHAR(150),
    ciudad VARCHAR(100),
    titulo_bachiller BOOLEAN,
    libreta_de_ultimo_anio BOOLEAN,
    id_carrera_primera INT REFERENCES carrera(id) ON DELETE SET NULL,
    id_carrera_segunda INT REFERENCES carrera(id) ON DELETE SET NULL,
    id_grupo INT REFERENCES grupo(id_grupo) ON DELETE SET NULL,
    id_gestion INT REFERENCES gestion(id_gestion) ON DELETE SET NULL
);

CREATE TABLE docente_grupo (
    id SERIAL PRIMARY KEY,
    id_docente INT REFERENCES docente(id_docente) ON DELETE CASCADE,
    id_grupo INT REFERENCES grupo(id_grupo) ON DELETE CASCADE,
    id_materia INT REFERENCES materia(id_materia) ON DELETE CASCADE
);

CREATE TABLE notas (
    id_postulante INT REFERENCES postulante(id_postulante) ON DELETE CASCADE,
    id_materia INT REFERENCES materia(id_materia) ON DELETE CASCADE,
    examen1 NUMERIC(5, 2),
    examen2 NUMERIC(5, 2),
    examen3 NUMERIC(5, 2),
    promedio NUMERIC(5, 2),
    estado VARCHAR(50),
    PRIMARY KEY (id_postulante, id_materia)
);

CREATE TABLE admision_final (
    id_postulante INT REFERENCES postulante(id_postulante),
    id_carrera_admitida INT REFERENCES carrera(id),
    nota_final_cup NUMERIC(5,2),
    opcion_ingreso VARCHAR(20),
    PRIMARY KEY (id_postulante)
);

-- Tabla de pagos para registrar transacciones PayPal
CREATE TABLE pago (
    id_pago SERIAL PRIMARY KEY,
    id_postulante INT REFERENCES postulante(id_postulante) ON DELETE CASCADE,
    monto NUMERIC(10, 2) NOT NULL DEFAULT 700.00,
    moneda VARCHAR(10) NOT NULL DEFAULT 'USD',
    paypal_order_id VARCHAR(255),
    estado VARCHAR(50) NOT NULL DEFAULT 'PENDIENTE',
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================================
-- TRIGGERS
-- ==========================================================

-- Control de Cupos por Grupo
CREATE OR REPLACE FUNCTION fn_verificar_cupo_grupo()
RETURNS TRIGGER AS $$
DECLARE
    v_capacidad_maxima INT;
    v_actuales_inscritos INT;
BEGIN
    SELECT capacidad_maxima INTO v_capacidad_maxima
    FROM grupo WHERE id_grupo = NEW.id_grupo;

    IF NEW.id_grupo IS NULL THEN
        RETURN NEW;
    END IF;

    SELECT COUNT(*) INTO v_actuales_inscritos
    FROM postulante WHERE id_grupo = NEW.id_grupo;

    IF v_actuales_inscritos >= v_capacidad_maxima THEN
        RAISE EXCEPTION 'Error: El grupo con ID % ya alcanzó su capacidad máxima de % estudiantes.',
            NEW.id_grupo, v_capacidad_maxima;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_verificar_cupo_grupo
BEFORE INSERT OR UPDATE OF id_grupo ON postulante
FOR EACH ROW
EXECUTE FUNCTION fn_verificar_cupo_grupo();

-- Validar Requisitos antes de Asignar Grupo
CREATE OR REPLACE FUNCTION fn_verificar_requisitos_postulante()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.id_grupo IS NOT NULL THEN
        IF NEW.titulo_bachiller = FALSE OR NEW.libreta_de_ultimo_anio = FALSE
           OR NEW.titulo_bachiller IS NULL OR NEW.libreta_de_ultimo_anio IS NULL THEN
            RAISE EXCEPTION 'Error: El postulante con ID % no cumple requisitos obligatorios.',
                NEW.id_postulante;
        END IF;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_verificar_requisitos_postulante
BEFORE INSERT OR UPDATE OF id_grupo, titulo_bachiller, libreta_de_ultimo_anio ON postulante
FOR EACH ROW
EXECUTE FUNCTION fn_verificar_requisitos_postulante();

-- Limitar Carga Horaria del Docente (Máx 5 grupos, sin cruces)
CREATE OR REPLACE FUNCTION fn_validar_restricciones_docente()
RETURNS TRIGGER AS $$
DECLARE
    v_cantidad_grupos INT;
    v_id_horario_nuevo INT;
    v_tiene_cruce INT;
BEGIN
    SELECT COUNT(DISTINCT id_grupo) INTO v_cantidad_grupos
    FROM docente_grupo WHERE id_docente = NEW.id_docente;

    IF v_cantidad_grupos >= 5 THEN
        RAISE EXCEPTION 'Error: El docente con ID % alcanzó el límite de 5 grupos.',
            NEW.id_docente;
    END IF;

    SELECT id_horario INTO v_id_horario_nuevo FROM grupo WHERE id_grupo = NEW.id_grupo;

    SELECT COUNT(*) INTO v_tiene_cruce
    FROM docente_grupo dg
    JOIN grupo g ON dg.id_grupo = g.id_grupo
    WHERE dg.id_docente = NEW.id_docente
      AND g.id_horario = v_id_horario_nuevo;

    IF v_tiene_cruce > 0 THEN
        RAISE EXCEPTION 'Error: Cruce de horarios para docente ID % en grupo %.',
            NEW.id_docente, NEW.id_grupo;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_validar_restricciones_docente
BEFORE INSERT ON docente_grupo
FOR EACH ROW
EXECUTE FUNCTION fn_validar_restricciones_docente();

-- Cálculo Automático de Promedios
CREATE OR REPLACE FUNCTION fn_calcular_promedio_notas()
RETURNS TRIGGER AS $$
DECLARE
    p_ex1 NUMERIC(5,2);
    p_ex2 NUMERIC(5,2);
    p_ex3 NUMERIC(5,2);
BEGIN
    SELECT porcentaje_examen1, porcentaje_examen2, porcentaje_examen3
    INTO p_ex1, p_ex2, p_ex3
    FROM materia WHERE id_materia = NEW.id_materia;

    IF p_ex1 IS NULL OR p_ex2 IS NULL OR p_ex3 IS NULL THEN
        p_ex1 := 33.33; p_ex2 := 33.33; p_ex3 := 33.34;
    END IF;

    NEW.promedio := ROUND(
        ((COALESCE(NEW.examen1, 0) * p_ex1) / 100) +
        ((COALESCE(NEW.examen2, 0) * p_ex2) / 100) +
        ((COALESCE(NEW.examen3, 0) * p_ex3) / 100), 2
    );

    IF NEW.promedio >= 60.00 THEN
        NEW.estado := 'APROBADO';
    ELSE
        NEW.estado := 'REPROBADO';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_calcular_promedio_notas
BEFORE INSERT OR UPDATE OF examen1, examen2, examen3 ON notas
FOR EACH ROW
EXECUTE FUNCTION fn_calcular_promedio_notas();

-- ==========================================================
-- PROCEDIMIENTO ALMACENADO: Admisión Final
-- ==========================================================
CREATE OR REPLACE PROCEDURE pr_procesar_admision_cup(p_id_gestion INT)
AS $$
DECLARE
    r_estudiante RECORD;
    v_cupos_ocupados INT;
    v_cupo_max_carrera INT;
    v_reprobado INT;
BEGIN
    DELETE FROM admision_final WHERE id_postulante IN (
        SELECT id_postulante FROM postulante WHERE id_gestion = p_id_gestion
    );

    FOR r_estudiante IN
        SELECT p.id_postulante, p.id_carrera_primera, p.id_carrera_segunda,
               ROUND(AVG(n.promedio), 2) as promedio_general
        FROM postulante p
        JOIN notas n ON p.id_postulante = n.id_postulante
        WHERE p.id_gestion = p_id_gestion
        GROUP BY p.id_postulante, p.id_carrera_primera, p.id_carrera_segunda
        ORDER BY promedio_general DESC
    LOOP
        SELECT COUNT(*) INTO v_reprobado
        FROM notas
        WHERE id_postulante = r_estudiante.id_postulante AND estado = 'REPROBADO';

        IF v_reprobado = 0 THEN
            SELECT cupo_maximo INTO v_cupo_max_carrera FROM carrera WHERE id = r_estudiante.id_carrera_primera;
            SELECT COUNT(*) INTO v_cupos_ocupados FROM admision_final WHERE id_carrera_admitida = r_estudiante.id_carrera_primera;

            IF v_cupos_ocupados < v_cupo_max_carrera THEN
                INSERT INTO admision_final (id_postulante, id_carrera_admitida, nota_final_cup, opcion_ingreso)
                VALUES (r_estudiante.id_postulante, r_estudiante.id_carrera_primera, r_estudiante.promedio_general, 'PRIMERA OPCION');
            ELSIF r_estudiante.id_carrera_segunda IS NOT NULL THEN
                SELECT cupo_maximo INTO v_cupo_max_carrera FROM carrera WHERE id = r_estudiante.id_carrera_segunda;
                SELECT COUNT(*) INTO v_cupos_ocupados FROM admision_final WHERE id_carrera_admitida = r_estudiante.id_carrera_segunda;

                IF v_cupos_ocupados < v_cupo_max_carrera THEN
                    INSERT INTO admision_final (id_postulante, id_carrera_admitida, nota_final_cup, opcion_ingreso)
                    VALUES (r_estudiante.id_postulante, r_estudiante.id_carrera_segunda, r_estudiante.promedio_general, 'SEGUNDA OPCION');
                END IF;
            END IF;
        END IF;
    END LOOP;

    RAISE NOTICE 'Proceso de admisión finalizado para la gestión %.', p_id_gestion;
END;
$$ LANGUAGE plpgsql;
