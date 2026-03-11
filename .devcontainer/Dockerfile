# Utilisation de l'image officielle PHP 8.4 CLI sur Debian Bookworm
FROM php:8.4-cli-bookworm

USER root

# 1. Installation des dépendances système
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

# 2. Installation des extensions PHP pour Symfony
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl zip pdo_mysql opcache bcmath gd mbstring

# 3. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Installation de Node.js et Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && corepack enable && corepack prepare yarn@stable --activate

# 5. Installation de Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# 6. Création de l'utilisateur vscode
RUN useradd -m vscode -s /bin/bash && \
    echo "vscode ALL=(ALL) NOPASSWD:ALL" >> /etc/apt/sudoers

# --- INSTALLATION DE SYMFONY ---
# On se place dans le dossier de travail
WORKDIR /workspaces/Projet-Canis-Pro

# On donne les droits à vscode sur le dossier avant d'installer
RUN chown -R vscode:vscode /workspaces/Projet-Canis-Pro

USER vscode

# Dossier de travail dans Codespaces
WORKDIR /workspaces/Projet-Canis-Pro

# Installation de Symfony 8 (Skeleton + Webapp)
# On utilise ^8.0 pour garantir la version 8 sans bloquer sur le patch 8.0.3
RUN composer create-project symfony/skeleton:"^8.0" . --no-interaction \
    && composer require webapp --no-interaction

# On s'assure que le terminal s'ouvre au bon endroit
WORKDIR /workspaces/Projet-Canis-Pro