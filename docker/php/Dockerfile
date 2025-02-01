# docker/php/Dockerfile

FROM php:8.2-fpm

# Installation de dépendances système
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install intl zip pdo pdo_mysql

# Installer Composer (dernière version)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www/app

# Exposer le port 9000 (PHP-FPM)
EXPOSE 9000

# Démarre PHP-FPM au lancement du conteneur
CMD ["php-fpm"]
