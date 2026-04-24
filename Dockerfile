FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev \
    && docker-php-ext-install zip bcmath pdo_mysql

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# 🔥 Laravel necesita esto
RUN cp .env.example .env

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generar key
RUN php artisan key:generate

# Frontend
RUN npm install && npm run build

EXPOSE 3000
CMD php artisan serve --host=0.0.0.0 --port=3000