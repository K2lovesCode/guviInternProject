# Use the official PHP image with Apache
FROM php:8.2-apache

# 1. Install system dependencies for MongoDB & Redis
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libssl-dev \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# 2. Install MongoDB & Redis via PECL
RUN pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy project files
COPY . .

# 6. Install PHP dependencies
# --no-dev optimizes for production
# --optimize-autoloader speeds up loading
# --ignore-platform-reqs safeguards against local/container mismatches
RUN composer install --no-dev --optimize-autoloader

# 7. Configure Apache DocumentRoot (if needed) or just expose port
# Apache defaults to /var/www/html which is perfect
EXPOSE 80

# 8. Start Apache
CMD ["apache2-foreground"]
