# Use an official PHP image with required extensions
FROM docker.arvancloud.ir/php:8.2-fpm

# Install Node.js (for Laravel Mix/Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Set working directory
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpq-dev \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nano \
    libzip-dev

# Install Nginx (for web server)
RUN apt-get install -y nginx

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl gd xml


# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY . /var/www

# Set permissions (adjust if you use Laravel's storage/logs setup)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Copy default Nginx config (will be mounted/overridden in compose)
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

# Expose port 9000 and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]

# Expose HTTP port for Nginx
EXPOSE 80
