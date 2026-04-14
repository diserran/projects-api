# 1. Imagen base
FROM php:8.3-fpm

# 2. Instalar dependencias del sistema requeridas
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# 3. Instalar y habilitar extensiones de PHP
RUN docker-php-ext-configure calendar \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd calendar zip intl

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js y NPM
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# 5. Configurar el directorio de trabajo
WORKDIR /var/www/laravel


# 6. Copiar SOLO los archivos de composer primero (Esto optimiza la caché de Docker)
COPY laravel/composer.json laravel/composer.lock* ./

# 7. Instalar dependencias de Composer (sin scripts ni autoloader todavía)
RUN composer install --no-scripts --no-autoloader

# 8. Copiar el resto del código de tu proyecto al contenedor
COPY . .

# 9. Generar el archivo autoload optimizado y ejecutar scripts
RUN composer dump-autoload --optimize --no-scripts
