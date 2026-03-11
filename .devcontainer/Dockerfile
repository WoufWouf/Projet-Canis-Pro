# Utilisation de l'image officielle PHP 8.4 CLI sur Debian Bookworm
FROM php:8.4-cli-bookworm

USER root

# Installation des dépendances système
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y --no-install-recommends \
       curl \
       git \
       unzip \
       libicu-dev \
       libzip-dev \
       ca-certificates \
       sudo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP pour Symfony
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl zip pdo_mysql opcache bcmath gd mbstring

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation de Yarn (via Corepack, intégré à Node)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && corepack enable && corepack prepare yarn@stable --activate

# Installation de Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Création de l'utilisateur vscode (pour correspondre à ton devcontainer.json)
RUN useradd -m vscode && \
    echo "vscode ALL=(ALL) NOPASSWD:ALL" >> /etc/apt/sudoers

USER vscode