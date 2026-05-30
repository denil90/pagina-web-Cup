# ==========================================
# Etapa 1: PHP Apache (Servidor de Producción)
# ==========================================
FROM php:8.4-apache

# 1. Instalar dependencias puras (sin Node.js para evitar conflictos con Apache MPM)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# 3. Configurar Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Aumentar límites para subida de PDFs (20MB)
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# 5. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Preparar entorno
WORKDIR /var/www/html

# Copiar el código fuente
COPY . .

# 7. Engaño temporal con .env para el build
RUN cp .env.example .env

# 8. Instalar librerías de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Configurar carpetas y permisos
RUN mkdir -p /var/www/html/storage/app/public /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Comandos Artisan finales
RUN php artisan key:generate
RUN php artisan storage:link

# 11. Limpieza
RUN rm .env

# Exponer el puerto
EXPOSE 80

# Iniciar el servidor asegurando que no haya conflictos de MPM y usando el puerto de Railway
CMD bash -c "a2dismod mpm_event mpm_worker; a2enmod mpm_prefork rewrite; sed -i 's/80/${PORT:-80}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf; apache2-foreground"
