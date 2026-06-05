FROM php:8.2-fpm-alpine

# Install essential packages & Supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    git \
    curl \
    zip \
    unzip \
    postgresql-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring pcntl bcmath gd

# Create necessary directories for Nginx and Supervisor
RUN mkdir -p /run/nginx /var/log/supervisor

# Configure Nginx & Supervisor (We will copy the configs shortly)
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Supervisor which will run both Nginx and PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
