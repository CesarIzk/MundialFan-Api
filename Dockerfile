FROM php:8.2-apache

# Extensiones necesarias para MySQL + Eloquent
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto
WORKDIR /var/www/html
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Apuntar Apache al public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Activar mod_rewrite para Slim
RUN a2enmod rewrite

# Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80