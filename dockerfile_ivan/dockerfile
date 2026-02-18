# 1. Imagen base
FROM php:8.2-apache-bullseye

# 2. Instalar dependencias del sistema con reintentos
# Añadimos limpieza de listas para asegurar que el update sea fresco
RUN apt-get clean && rm -rf /var/lib/apt/lists/* && \
    apt-get update --fix-missing && \
    apt-get install -y --no-install-recommends \
    git curl libpng-dev libonig-dev libxml2-dev libpq-dev zip unzip openssl && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Instalar Node.js 23.x
RUN curl -fsSL https://deb.nodesource.com/setup_23.x | bash - \
    && apt-get install -y nodejs && apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Instalar extensiones PHP
RUN docker-php-ext-install pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd

# 5. Activar módulos Apache
RUN a2enmod rewrite ssl

# 6. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. Generar Certificado SSL
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/selfsigned.key \
    -out /etc/ssl/certs/selfsigned.crt \
    -subj "/C=ES/ST=Madrid/L=Madrid/O=EliteDrive/OU=IT/CN=localhost"

# 8. Configurar Apache
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# 9. Directorio de trabajo
WORKDIR /var/www/html
RUN git config --global --add safe.directory /var/www/html

# 10. Copiar el código
COPY . .

# 11. Limpieza de restos de Windows
RUN rm -rf node_modules package-lock.json

# 12. Instalar dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 13. Compilación de JS con límite de memoria (1024MB = 1GB)
ENV NODE_OPTIONS="--max-old-space-size=1024"
RUN npm install --legacy-peer-deps && npm run build

# 14. Permisos finales
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80 443