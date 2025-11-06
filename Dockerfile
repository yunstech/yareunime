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
    libzip-dev libonig-dev libpq-dev pkg-config zip unzip git curl vim cron supervisor locales \
    nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ============================================
# 🧩 PHP extensions
# ============================================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring zip exif pcntl pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis

# ============================================
# 🧩 Composer & Node.js
# ============================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# ============================================
# 🧩 Copy source code
# ============================================
COPY . /var/www

# ============================================
# 🧩 Build Frontend (Vite)
# ============================================
RUN npm ci && npm run build

# ============================================
# ⚙️ Configure Nginx
# ============================================
COPY Docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default

# ============================================
# ⚙️ Supervisor configuration
# ============================================
RUN mkdir -p /var/log/supervisor
COPY Docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ============================================
# ✅ Permissions
# ============================================
RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ============================================
# 🌍 Expose web port
# ============================================
EXPOSE 80

# ============================================
# 🏁 Start Supervisor (which runs PHP-FPM + Nginx)
# ============================================
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
