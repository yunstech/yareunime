# =======================================================
# Stage 1: Build Frontend (Vite + Tailwind)
# =======================================================
FROM node:20-slim AS node_builder

WORKDIR /app

# Salin file package.json & lock untuk caching layer
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy semua file project
COPY . .

# Jalankan build Vite (Tailwind, JS, dll)
RUN npm run build


# =======================================================
# Stage 2: Laravel + PHP 8.3 + Composer
# =======================================================
FROM php:8.3-fpm

# Install dependencies yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpq-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip mbstring xml

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /var/www/html

# Copy source project
COPY . .

# Copy hasil build frontend dari stage node
COPY --from=node_builder /app/public/build ./public/build

# Install PHP dependencies (composer)
RUN composer install --no-interaction --no-progress --prefer-dist

# Set permissions untuk Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port (FPM)
EXPOSE 9000

# Jalankan php-fpm
CMD ["php-fpm"]
