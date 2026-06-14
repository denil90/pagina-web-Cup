#!/bin/bash
set -e

# Asegurar directorios de almacenamiento en tiempo de ejecución (útil para volúmenes montados)
echo "Configurando directorios de almacenamiento..."
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

# Ajustar permisos para que el servidor web pueda escribir
echo "Ajustando permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Recrear el enlace simbólico del almacenamiento público
echo "Enlazando almacenamiento público..."
php artisan storage:link --force || true

# Configurar Apache
echo "Configurando módulos de Apache..."
a2dismod mpm_event mpm_worker || true
a2enmod mpm_prefork rewrite || true

# Cambiar el puerto por defecto de Apache al asignado por Railway
echo "Configurando puerto de escucha a ${PORT:-80}..."
sed -i "s/80/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Limpiar caché de configuración para leer variables de entorno actualizadas de Railway
echo "Limpiando caché de configuración..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Arrancar Apache
echo "Iniciando Apache..."
exec apache2-foreground
