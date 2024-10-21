# Use PHP with Apache as the base image
FROM php:8.3-fpm

ARG user

ARG uid

# Install packages for my machine
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache(optional)
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

#Install PHP Extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

#Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user


WORKDIR /var/www

USER $user

EXPOSE 9000

CMD ["php-fpm"]

ENTRYPOINT ["/var/www/docker-compose/startup-commands/run.sh"]
