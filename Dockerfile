# FROM mcr.microsoft.com/devcontainers/php:8.2

# RUN apt-get update && apt-get install -y \
#     default-mysql-client \
#     libzip-dev \
#     unzip \
#     zip \
#     && docker-php-ext-install pdo_mysql mysqli \
#     && apt-get clean \
#     && rm -rf /var/lib/apt/lists/*

FROM mcr.microsoft.com/devcontainers/php:8.2

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libzip-dev \
    unzip \
    zip \
    && docker-php-ext-install pdo_mysql mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /workspaces/apec-mavic