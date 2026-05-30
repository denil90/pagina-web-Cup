# Sistema CUP - Curso Preuniversitario FIC

Bienvenido al sistema web integral para la gestión de inscripciones, control de pagos y administración del Curso Universitario Preuniversitario (CUP). Este proyecto está construido con **Laravel 13**, enfocado en una experiencia de usuario moderna y una arquitectura sólida basada en roles.

✨ **Características Principales**
- **Diseño Premium:** Interfaz moderna, responsiva y estilizada con CSS nativo y sistema de variables.
- **Gestión por Roles:** Vistas completamente diferenciadas para Administradores, Postulantes y Docentes.
- **Validación Estricta:** Bloqueos a nivel de base de datos (PostgreSQL) mediante *Triggers* para evitar saltarse pasos (ej. no pagar sin subir PDFs).
- **Pasarela Integrada:** Pagos automáticos utilizando la API de PayPal (Sandbox/Producción).
- **Subida de Archivos:** Gestión segura de PDFs para revisión de Título de Bachiller y Libreta.

---

## 🚀 Guía de Instalación Local
Si acabas de clonar el repositorio, sigue estos pasos para configurar tu entorno de desarrollo:

### 1. Instalar dependencias
Asegúrate de tener PHP (>= 8.4) y Composer instalados. Luego ejecuta:
```bash
composer install
npm install
npm run build
```

### 2. Configurar el archivo de entorno
Copia el archivo de ejemplo y configura tus credenciales:
```bash
cp .env.example .env
php artisan key:generate
```
*Nota: Abre el archivo `.env` y ajusta `DB_CONNECTION=pgsql`, así como `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` según tu configuración de PostgreSQL. También añade tu `PAYPAL_CLIENT_ID`.*

### 3. Configurar la Base de Datos (Estructura)
Este proyecto utiliza un archivo SQL puro con funciones y triggers en lugar de migraciones estándar.
1. Crea una base de datos vacía (ej. `escuela`) en PostgreSQL.
2. Ejecuta todo el contenido del archivo `database/sql/database.sql` en tu gestor (pgAdmin, DBeaver, etc).

### 4. Poblar datos iniciales y enlazar storage
```bash
php artisan db:seed
php artisan storage:link
```

### 5. Iniciar el sistema
```bash
php artisan serve
```

---

## 🔐 Credenciales de Acceso (Prueba)
Una vez configurado y "sembrado" (seeded), puedes entrar con la cuenta de administrador generada automáticamente:

- **Email:** `admin@cup.fic.edu.bo`
- **Password:** `admin123`

---

## ☁️ Guía Rápida para Producción (Railway)

El repositorio está optimizado con un `Dockerfile` Multi-Etapa, asegurando un despliegue sin conflictos.

1. **Variables de Entorno:** Configura en Railway tus accesos de DB (`DB_HOST`, `DB_PORT`, etc.) y `APP_KEY`.
2. **Volúmenes (Crítico):** Para que los PDFs no se borren en cada actualización, añade un Volumen en Railway apuntando a:
   `/var/www/html/storage/app/public`
3. **Poblar DB:** Desde la consola de base de datos en Railway, ejecuta el SQL de `database/sql/database.sql`. Luego entra a la Terminal web del contenedor y corre `php artisan db:seed`.