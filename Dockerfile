FROM php:8.3-apache

# 1. Instalar dependencias del sistema requeridas por Laravel y PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    nodejs \
    npm

# Limpiar caché de apt para reducir el tamaño de la imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# 3. Configurar Apache para que apunte a la carpeta "public" de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 4. Aumentar el límite de subida de archivos de PHP (Soluciona el error al subir PDFs)
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# 5. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecer el directorio de trabajo
WORKDIR /var/www/html

# 7. Copiar los archivos de la aplicación
COPY . .

# 8. Instalar dependencias de PHP (sin paquetes de desarrollo)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Construir los assets de frontend (Vite)
RUN npm install
RUN npm run build

# 10. Configurar permisos para las carpetas críticas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Crear el enlace simbólico del storage
RUN php artisan storage:link

# Exponer el puerto 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
