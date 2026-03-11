# Base image
FROM mcr.microsoft.com/devcontainers/php:8.4

# Éviter les interactions lors de l'install
ENV DEBIAN_FRONTEND=noninteractive

# 1. Ajout du dépôt PHP Ondrej et Yarn
RUN apt-get update && apt-get install -y software-properties-common gpg curl \
    && add-apt-repository ppa:ondrej/php -y \
    && mkdir -p /etc/apt/keyrings \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor -o /etc/apt/keyrings/yarn.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian stable main" > /etc/apt/sources.list.d/yarn.list

# 2. Installation de PHP 8.4 et des outils
RUN apt-get update && apt-get install -y \
    php8.4 php8.4-cli php8.4-common php8.4-mbstring php8.4-xml php8.4-curl php8.4-mysql php8.4-zip php8.4-intl php8.4-bcmath \
    git unzip zip mariadb-client nodejs yarn \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Forcer PHP 8.4 par défaut
RUN update-alternatives --set php /usr/bin/php8.4 \
    && ln -sf /usr/bin/php8.4 /usr/local/bin/php

# 4. Installer Composer & Symfony CLI
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /workspace