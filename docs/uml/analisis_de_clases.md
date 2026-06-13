# Análisis de Clases de Robustez (Frontera-Control-Entidad)

Este documento contiene los diagramas de clases de análisis (BCE - Boundary-Control-Entity) para todos los casos de uso correspondientes a los Ciclos 1 y 2, estructurados y estilizados para parecerse exactamente a tu imagen de referencia.

## Estilo Visual y Estructura
* **Actores Reales:** Representados con el ícono del monigote (stickman) mediante el comando `actor`.
* **Cajas con Bordes Rectos:** Se desactivó el redondeado de esquinas (`roundCorner 0`).
* **Colores de Referencia:** Cajas color crema suave (`#FDF6ED`), bordes marrones (`#7A5F48`) y sombras activadas.
* **Conexiones Simples:** Líneas rectas (`-`) sin puntas de flecha, tal como se muestra en la captura.
* **Títulos Claros:** Cada diagrama incluye el nombre del caso de uso arriba (`title CUXX NOMBRE_CASO`).

---

# Ciclo 1

## CU01: Registrarse como Postulante
```plantuml
@startuml
title CU01 REGISTRARSE COMO POSTULANTE

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor postulante

class "vista registrar postulante" as Vista {
    - nombre : string
    - apellidos : string
    - ci : string
    - contrasena : string
    - fechanac : date
    - sexo : char
    - direccion : string
    - telefono : string
    - correo : string
    - colegio_procedencia : string
    - ciudad : string
    - id_carrera_primera : int
    - id_carrera_segunda : int
    --
    + showRegistroPostulante() : void
    + registrarPostulante() : void
}

class "registro controler" as Controller {
    + showRegistroPostulante() : View
    + registrarPostulante(request) : Redirect
}

class "usuario" as EntityUsuario {
    - id_usuario : int
    - nombre : string
    - apellidos : string
    - ci : string
    - contrasena : string
    - fechanac : date
    - sexo : char
    - direccion : string
    - telefono : string
    - correo : string
    - rol : string
    - fecha : date
}

class "postulante" as EntityPostulante {
    - id_postulante : int
    - colegio_procedencia : string
    - ciudad : string
    - titulo_bachiller : bool
    - libreta_de_ultimo_anio : bool
    - id_carrera_primera : int
    - id_carrera_segunda : int
    - id_grupo : int
    - id_gestion : int
    - archivo_titulo_bachiller : string
    - archivo_libreta : string
}

postulante - Vista
Vista - Controller
Controller - EntityUsuario
Controller - EntityPostulante
EntityPostulante - EntityUsuario
@enduml
```

---

## CU02: Gestionar sesión
```plantuml
@startuml
title CU02 GESTIONAR SESION

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor usuario

class "vista gestionar sesion" as Vista {
    - correo : string
    - contrasena : string
    --
    + showLoginForm() : void
    + login() : void
    + logout() : void
}

class "login controler" as Controller {
    + showLoginForm() : View
    + login(request) : Redirect
    + logout(request) : Redirect
    - redirigirSegunRol(usuario) : Redirect
}

class "usuario" as EntityUsuario {
    - id_usuario : int
    - correo : string
    - contrasena : string
    - rol : string
}

usuario - Vista
Vista - Controller
Controller - EntityUsuario
@enduml
```

---

## CU04: Visualizar Dashboard del Postulante
```plantuml
@startuml
title CU04 VISUALIZAR DASHBOARD DEL POSTULANTE

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor postulante

class "vista dashboard" as Vista {
    --
    + mostrarDashboard() : void
}

class "dashboard controler" as Controller {
    + index() : View
    - determinarEstado(postulante) : array
}

class "postulante" as EntityPostulante {
    - id_postulante : int
    - colegio_procedencia : string
    - id_grupo : int
    - id_gestion : int
}

postulante - Vista
Vista - Controller
Controller - EntityPostulante
@enduml
```

---

## CU05: Pago de inscripción
```plantuml
@startuml
title CU05 PAGO DE INSCRIPCION

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor postulante

class "vista pago inscripcion" as Vista {
    - paypal_order_id : string
    --
    + mostrarFormularioPago() : void
    + enviarCreacionPago() : void
    + enviarConfirmacionPago() : void
    + simularPago() : void
}

class "pago controler" as Controller {
    + index() : View
    + crearPago() : Redirect
    + confirmar(request) : JsonResponse
    + simularPago() : Redirect
}

class "pago service" as Service {
    + obtenerEstadoPago(id) : Pago
    + obtenerMontoInscripcion() : float
    + obtenerMoneda() : string
    + estaEnModoTest() : bool
    + crearPagoPendiente(id) : void
    + confirmarPago(id, orderId) : void
}

class "postulante" as EntityPostulante {
    - id_postulante : int
    --
    + cumpleRequisitos() : bool
    + tienePagoConfirmado() : bool
}

class "pago" as EntityPago {
    - id_pago : int
    - id_postulante : int
    - monto : float
    - estado : string
    - paypal_order_id : string
}

postulante - Vista
Vista - Controller
Controller - Service
Controller - EntityPostulante
Service - EntityPago
EntityPostulante - EntityPago
@enduml
```

---

## CU07: Gestionar Periodo
```plantuml
@startuml
title CU07 GESTIONAR PERIODO

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar periodo" as Vista {
    - semestre : string
    - anio : int
    --
    + mostrarGestiones() : void
    + formularioCrear() : void
    + formularioEditar() : void
    + enviarCreacion() : void
    + enviarActualizacion() : void
    + enviarEliminacion() : void
}

class "periodo controler" as Controller {
    + index() : View
    + create() : View
    + store(request) : Redirect
    + edit(id) : View
    + update(request, id) : Redirect
    + destroy(id) : Redirect
}

class "gestion" as EntityGestion {
    - id_gestion : int
    - semestre : string
    - anio : int
}

administrador - Vista
Vista - Controller
Controller - EntityGestion
@enduml
```

---

## CU08: Gestionar Carrera
```plantuml
@startuml
title CU08 GESTIONAR CARRERA

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar carrera" as Vista {
    - nombre : string
    - descripcion : string
    - cupo_maximo : int
    --
    + mostrarCarreras() : void
    + formularioCrear() : void
    + formularioEditar() : void
    + enviarCreacion() : void
    + enviarActualizacion() : void
    + enviarEliminacion() : void
}

class "carrera controler" as Controller {
    + index() : View
    + create() : View
    + store(request) : Redirect
    + edit(id) : View
    + update(request, id) : Redirect
    + destroy(id) : Redirect
}

class "carrera" as EntityCarrera {
    - id : int
    - nombre : string
    - descripcion : string
    - cupo_maximo : int
}

administrador - Vista
Vista - Controller
Controller - EntityCarrera
@enduml
```

---

## CU09: Gestionar Materias
```plantuml
@startuml
title CU09 GESTIONAR MATERIAS

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar materias" as Vista {
    - nombre : string
    - porcentaje_examen1 : float
    - porcentaje_examen2 : float
    - porcentaje_examen3 : float
    --
    + mostrarMaterias() : void
    + formularioCrear() : void
    + formularioEditar() : void
    + enviarCreacion() : void
    + enviarActualizacion() : void
    + enviarEliminacion() : void
}

class "materia controler" as Controller {
    + index() : View
    + create() : View
    + store(request) : Redirect
    + edit(id) : View
    + update(request, id) : Redirect
    + destroy(id) : Redirect
}

class "materia" as EntityMateria {
    - id_materia : int
    - nombre : string
    - porcentaje_examen1 : float
    - porcentaje_examen2 : float
    - porcentaje_examen3 : float
}

administrador - Vista
Vista - Controller
Controller - EntityMateria
@enduml
```

---

## CU11: Gestionar Aula
```plantuml
@startuml
title CU11 GESTIONAR AULA

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar aula" as Vista {
    - nombre : string
    - edificio : string
    - capacidad : int
    --
    + mostrarAulas() : void
    + enviarCreacion() : void
    + enviarActualizacion() : void
    + enviarEliminacion() : void
}

class "aula controler" as Controller {
    + index() : View
    + store(request) : Redirect
    + update(request, id) : Redirect
    + destroy(id) : Redirect
}

class "aula" as EntityAula {
    - id_aula : int
    - nombre : string
    - edificio : string
    - capacidad : int
}

administrador - Vista
Vista - Controller
Controller - EntityAula
@enduml
```

---

## CU12: Gestionar Horarios
```plantuml
@startuml
title CU12 GESTIONAR HORARIOS

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar horarios" as Vista {
    - dia : string
    - hora_inicio : time
    - hora_final : time
    --
    + mostrarHorarios() : void
    + enviarCreacion() : void
    + enviarActualizacion() : void
    + enviarEliminacion() : void
}

class "horarios controler" as Controller {
    + index() : View
    + store(request) : Redirect
    + update(request, id) : Redirect
    + destroy(id) : Redirect
}

class "horario" as EntityHorario {
    - id_horario : int
    - dia : string
    - hora_inicio : time
    - hora_final : time
}

administrador - Vista
Vista - Controller
Controller - EntityHorario
@enduml
```

---

## CU16: Administrar y Verificar Requisitos de Postulantes
```plantuml
@startuml
title CU16 ADMINISTRAR Y VERIFICAR REQUISITOS DE POSTULANTES

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista verificar requisitos" as Vista {
    - titulo_bachiller : bool
    - libreta_de_ultimo_anio : bool
    --
    + mostrarPostulantes() : void
    + mostrarDetalleRequisitos() : void
    + enviarVerificacion() : void
}

class "requisitos controler" as Controller {
    + index(request) : View
    + show(id) : View
    + verificarRequisitos(request, id) : Redirect
}

class "postulante" as EntityPostulante {
    - id_postulante : int
    - titulo_bachiller : bool
    - libreta_de_ultimo_anio : bool
    - archivo_titulo_bachiller : string
    - archivo_libreta : string
    --
    + cumpleRequisitos() : bool
}

class "usuario" as EntityUsuario {
    - id_usuario : int
    - nombre : string
    - apellidos : string
    - ci : string
}

administrador - Vista
Vista - Controller
Controller - EntityPostulante
EntityPostulante - EntityUsuario
@enduml
```

---
---

# Ciclo 2

## CU10: Gestionar Grupos
```plantuml
@startuml
title CU10 GESTIONAR GRUPOS

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar grupos" as Vista {
    - nombre : string
    - capacidad_maxima : int
    - id_horario : int
    - id_aula : int
    - id_turno : int
    --
    + mostrarGrupos() : void
    + formularioCrear() : void
    + formularioEditar() : void
    + enviarCreacion() : void
    + enviarActualizacion() : void
    + enviarEliminacion() : void
}

class "grupo controler" as Controller {
    + index() : View
    + create() : View
    + store(request) : Redirect
    + edit(id) : View
    + update(request,id) : Redirect
    + destroy(id) : Redirect
    + show(id) : View
}

class "grupo" as EntityGrupo {
    - id_grupo : int
    - nombre : string
    - capacidad_maxima : int
    - id_horario : int
    - id_aula : int
    - id_turno : int
    --
    + inscritosActuales() : int
    + tieneDisponibilidad() : bool
    + porcentajeOcupacion() : float
}

class "horario" as EntityHorario {
    - id_horario : int
    - nombre : string
}

class "aula" as EntityAula {
    - id_aula : int
    - nombre : string
    - capacidad : int
}

class "turno" as EntityTurno {
    - id_turno : int
    - nombre : string
}

administrador - Vista
Vista - Controller
Controller - EntityGrupo
EntityGrupo - EntityHorario
EntityGrupo - EntityAula
EntityGrupo - EntityTurno
@enduml
```

---

## CU13: Gestionar Turnos
```plantuml
@startuml
title CU13 GESTIONAR TURNOS

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar turnos" as Vista {
    - nombre : string
    --
    + mostrarTurnos() : void
    + enviarCreacion() : void
    + enviarEliminacion() : void
}

class "turno controler" as Controller {
    + index() : View
    + store(request) : Redirect
    + destroy(id) : Redirect
}

class "turno" as EntityTurno {
    - id_turno : int
    - nombre : string
}

administrador - Vista
Vista - Controller
Controller - EntityTurno
@enduml
```

---

## CU14: Gestionar Docentes
```plantuml
@startuml
title CU14 GESTIONAR DOCENTES

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista gestionar docentes" as Vista {
    - nombre : string
    - email : string
    - titulo_profesional : string
    - maestria : string
    - diplomado : string
    - estado : string
    --
    + mostrarDocentes() : void
    + formularioCrear() : void
    + formularioEditar() : void
    + enviarCreacion() : void
    + enviarActualizacion() : void
}

class "docente controler" as Controller {
    + index() : View
    + create() : View
    + store(request) : Redirect
    + edit(id) : View
    + update(request, id) : Redirect
}

class "docente" as EntityDocente {
    - id_docente : int
    - titulo_profesional : string
    - maestria : string
    - diplomado : string
    - estado : string
    --
    + estaActivo() : bool
    + cantidadGrupos() : int
}

class "usuario" as EntityUsuario {
    - id_usuario : int
    - nombre : string
    - email : string
}

administrador - Vista
Vista - Controller
Controller - EntityDocente
Controller - EntityUsuario
EntityDocente - EntityUsuario
@enduml
```

---

## CU15: Asignar Docentes a Grupos y Materias
```plantuml
@startuml
title CU15 ASIGNAR DOCENTES A GRUPOS Y MATERIAS

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista asignar docente" as Vista {
    - id_docente : int
    - id_grupo : int
    - id_materia : int
    --
    + mostrarFormularioAsignacion() : void
    + enviarAsignacion() : void
    + enviarRemoverAsignacion() : void
}

class "docente controler" as Controller {
    + showAsignar(id) : View
    + asignar(request, id) : Redirect
    + removerAsignacion(id) : Redirect
}

class "docenteGrupo" as EntityDocenteGrupo {
    - id : int
    - id_docente : int
    - id_grupo : int
    - id_materia : int
}

class "docente" as EntityDocente {
    - id_docente : int
    - titulo_profesional : string
}

class "grupo" as EntityGrupo {
    - id_grupo : int
    - nombre : string
}

class "materia" as EntityMateria {
    - id_materia : int
    - nombre : string
}

administrador - Vista
Vista - Controller
Controller - EntityDocenteGrupo
EntityDocenteGrupo - EntityDocente
EntityDocenteGrupo - EntityGrupo
EntityDocenteGrupo - EntityMateria
@enduml
```

---

## CU17: Asignar Grupo a Postulante
```plantuml
@startuml
title CU17 ASIGNAR GRUPO A POSTULANTE

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista asignar grupo" as Vista {
    - id_postulante : int
    - id_grupo : int
    --
    + mostrarDetallePostulante() : void
    + enviarAsignacionGrupo() : void
}

class "postulante controler" as Controller {
    + show(id) : View
    + asignarGrupo(request, id) : Redirect
}

class "postulante" as EntityPostulante {
    - id_postulante : int
    - id_grupo : int
    --
    + cumpleRequisitos() : bool
    + tienePagoConfirmado() : bool
}

class "grupo" as EntityGrupo {
    - id_grupo : int
    - nombre : string
    - capacidad_maxima : int
    --
    + tieneDisponibilidad() : bool
    + inscritosActuales() : int
}

administrador - Vista
Vista - Controller
Controller - EntityPostulante
Controller - EntityGrupo
EntityPostulante - EntityGrupo
@enduml
```

---

## CU18: Registrar y Modificar Calificaciones
```plantuml
@startuml
title CU18 REGISTRAR Y MODIFICAR CALIFICACIONES

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor docente

class "vista registrar notas" as Vista {
    - examen1 : float
    - examen2 : float
    - examen3 : float
    --
    + filtrarPostulantes() : void
    + formularioRegistro() : void
    + enviarNotas() : void
}

class "notas controler" as Controller {
    + index() : View
    + registrar(postulante, materia) : View
    + guardar(request) : Redirect
}

class "nota" as EntityNota {
    - id_postulante : int
    - id_materia : int
    - examen1 : float
    - examen2 : float
    - examen3 : float
    - promedio : float
    - estado : string
    --
    + estaAprobado() : bool
}

class "postulante" as EntityPostulante {
    - id_postulante : int
}

class "materia" as EntityMateria {
    - id_materia : int
    - nombre : string
}

docente - Vista
Vista - Controller
Controller - EntityNota
EntityNota - EntityPostulante
EntityNota - EntityMateria
@enduml
```

---

## CU19: Procesar Algoritmo de Admisión
```plantuml
@startuml
title CU19 PROCESAR ALGORITMO DE ADMISION

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista admision" as Vista {
    - id_gestion : int
    --
    + mostrarModuloAdmision() : void
    + enviarProcesarAlgoritmo() : void
    + mostrarResultados() : void
}

class "admision controler" as Controller {
    + index() : View
    + procesar(request) : Redirect
    + resultados(gestion) : View
}

class "postulante" as EntityPostulante {
    - id_postulante : int
    - id_carrera_primera : int
    - id_carrera_segunda : int
    --
    + cumpleRequisitos() : bool
    + tienePagoConfirmado() : bool
    + aproboTodasLasMaterias() : bool
    + promedioGeneral() : float
}

class "carrera" as EntityCarrera {
    - id : int
    - nombre : string
    - cupos : int
}

class "admision final" as EntityAdmisionFinal {
    - id_postulante : int
    - id_carrera_admitida : int
    - nota_final_cup : float
    - opcion_ingreso : string
    --
    + fueAdmitidoEnPrimeraOpcion() : bool
}

class "gestion" as EntityGestion {
    - id_gestion : int
    - nombre : string
}

administrador - Vista
Vista - Controller
Controller - EntityPostulante
Controller - EntityCarrera
Controller - EntityAdmisionFinal
Controller - EntityGestion
EntityPostulante - EntityAdmisionFinal
EntityAdmisionFinal - EntityCarrera
EntityPostulante - EntityGestion
@enduml
```

---

## CU03: Consultar Resultados de Admisión (Pública)
```plantuml
@startuml
title CU03 CONSULTAR RESULTADOS DE ADMISION (PUBLICA)

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor publico

class "vista resultados publicos" as Vista {
    - ci_buscar : string
    --
    + formularioConsulta() : void
    + mostrarResultado() : void
}

class "resultado controler" as Controller {
    + consultaPublica(request) : View
}

class "postulante" as EntityPostulante {
    - id_postulante : int
    - id_carrera_primera : int
    - id_carrera_segunda : int
}

class "admision final" as EntityAdmisionFinal {
    - id_postulante : int
    - id_carrera_admitida : int
    - nota_final_cup : float
    - opcion_ingreso : string
}

class "carrera" as EntityCarrera {
    - id : int
    - nombre : string
}

publico - Vista
Vista - Controller
Controller - EntityPostulante
Controller - EntityAdmisionFinal
EntityPostulante - EntityAdmisionFinal
EntityAdmisionFinal - EntityCarrera
@enduml
```

---

## CU06: Consultar Calificaciones Propias
```plantuml
@startuml
title CU06 CONSULTAR CALIFICACIONES PROPIAS

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor postulante

class "vista mis notas" as Vista {
    --
    + mostrarPanelNotas() : void
}

class "resultado controler" as Controller {
    + misNotas() : View
}

class "postulante" as EntityPostulante {
    - id_postulante : int
}

class "nota" as EntityNota {
    - id_postulante : int
    - id_materia : int
    - examen1 : float
    - examen2 : float
    - examen3 : float
    - promedio : float
    - estado : string
}

class "materia" as EntityMateria {
    - id_materia : int
    - nombre : string
}

postulante - Vista
Vista - Controller
Controller - EntityPostulante
Controller - EntityNota
EntityPostulante - EntityNota
EntityNota - EntityMateria
@enduml
```

---

## CU20: Generar y Exportar Reportes
```plantuml
@startuml
title CU20 GENERAR Y EXPORTAR REPORTES

left to right direction

skinparam roundCorner 0
skinparam BackgroundColor #FFFFFF

skinparam class {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
    AttributeFontColor #4A3525
    AttributeFontSize 11
    AttributeFontName Arial
    MethodFontColor #4A3525
    MethodFontSize 11
    MethodFontName Arial
}

skinparam actor {
    BackgroundColor #FDF6ED
    BorderColor #7A5F48
    FontColor #4A3525
    FontSize 12
    FontName Arial
}

skinparam arrow {
    Color #7A6F62
    Thickness 1
}

actor administrador

class "vista reportes" as Vista {
    - id_gestion : int
    - id_grupo : int
    - tipo_reporte : string
    --
    + configurarFiltros() : void
    + previsualizarDatos() : void
    + exportarPdf() : void
    + exportarCsv() : void
}

class "reporte controler" as Controller {
    + index() : View
    + aprobadosPorGestion(request) : JsonResponse
    + rendimientoPorGrupo(request) : JsonResponse
    + docenteDestacado(request) : JsonResponse
    + comparativaGestiones(request) : JsonResponse
    + admitidosPorCarrera(request) : JsonResponse
    + exportarPdf(request) : Response
    + exportarCsv(request) : Response
}

class "postulante" as EntityPostulante {
}

class "nota" as EntityNota {
}

class "admision final" as EntityAdmisionFinal {
}

class "docente" as EntityDocente {
}

class "carrera" as EntityCarrera {
}

administrador - Vista
Vista - Controller
Controller - EntityPostulante
Controller - EntityNota
Controller - EntityAdmisionFinal
Controller - EntityDocente
Controller - EntityCarrera
@enduml
```
