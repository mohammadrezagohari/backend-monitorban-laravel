# Use an official PHP image with required extensions
FROM docker.arvancloud.ir/php:8.2-fpm

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

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl gd xml


# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY . /var/www

# Set permissions (adjust if you use Laravel's storage/logs setup)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose port 9000 and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
