# Étape 1 : Construire l'image PHP avec PHP-FPM
FROM php:8.2-fpm-alpine

# Installer les dépendances nécessaires
RUN apk update && apk add --no-cache \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    git \
    unzip \
    curl \
    shadow \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo_mysql \
    zip \
    gd \
    mbstring \
    opcache

# Installer Composer globalement
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurer PHP-FPM
RUN echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de configuration
COPY --chown=www-data:www-data . .

# Installer les dépendances Composer (en tant qu'utilisateur www-data)
USER www-data
RUN composer clear-cache && composer install --prefer-dist --no-progress --no-interaction --no-scripts

# Ajuster les permissions
RUN chown -R www-data:www-data var

EXPOSE 9000
CMD ["php-fpm"]