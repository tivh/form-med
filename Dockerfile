# Multi-stage build for Laravel (PHP 8.3, Vite assets, Postgres-ready)

# 1) Install PHP dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

# 2) Build front-end assets (optional: comment out if not using Vite)
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --no-audit --progress=false
COPY resources ./resources
COPY vite.config.js ./
COPY tailwind.config.js ./ 2>/dev/null || true
COPY postcss.config.js ./ 2>/dev/null || true
RUN npm run build

# 3) Runtime image
FROM php:8.3-cli-alpine
WORKDIR /var/www/html

# System deps for Laravel + Postgres
RUN apk add --no-cache libpq-dev libzip-dev oniguruma-dev bash \
    && docker-php-ext-install pdo_pgsql bcmath

# Increase PHP upload limits
RUN echo "upload_max_filesize=15M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=105M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# App source
COPY . ./
# Vendors and built assets
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Permissions for storage/cache
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
