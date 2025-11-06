# ============================================
# 🧩 Base Image: PHP-FPM 8.3 (for Laravel)
# ============================================
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# ============================================
# 🧱 Install system dependencies
# ============================================
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libonig-dev \
    libpq-dev pkg-config zip unzip git curl vim cron supervisor locales \
    ca-certificates \
    jpegoptim optipng pngquant gifsicle \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ============================================
# 🧩 Install PHP extensions
# ============================================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring zip exif pcntl pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis

# ============================================
# 🧩 Install Composer
# ============================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================================
# 🧩 Copy source files
# ============================================
COPY . /var/www

# ============================================
# 🧩 Install Node for Tailwind/Vite
# ============================================
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# ============================================
# 🧩 Install PHP dependencies (Composer)
# ============================================
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# ============================================
# 🧩 Build Frontend (Vite)
# ============================================
RUN npm ci && npm run build

# ============================================
# ⚙️ Supervisor configuration
# ============================================
RUN mkdir -p /var/log/supervisor
COPY Docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Example: inside supervisord.conf
# [program:php-fpm]
# command=php-fpm -F
# [program:queue-worker]
# command=php /var/www/artisan queue:listen --queue=scraping --sleep=3 --tries=3
# [program:scheduler]
# command=php /var/www/artisan schedule:work
# [program:serve]
# command=php artisan serve --host=0.0.0.0 --port=8000

# ============================================
# ✅ Permissions
# ============================================
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache


RUN npm install && npm run build
# ============================================
# 🌍 Expose Laravel port
# ============================================
EXPOSE 8000

# ============================================
# 🏁 Start Supervisor
# ============================================
CMD php artisan serve --host=0.0.0.0 --port=8000
