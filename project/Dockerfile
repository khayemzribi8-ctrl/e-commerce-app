FROM php:8.2-cli-alpine

WORKDIR /app

# copier app
COPY . .

# installer composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# dépendances système
RUN apk add --no-cache bash icu-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip

# installer dépendances PHP
RUN composer install --no-scripts --no-interaction --no-progress

# 🔥 créer user NON ROOT avec UID fixe
RUN addgroup -g 1000 appgroup && \
    adduser -D -u 1000 -G appgroup appuser

# permissions
RUN chown -R appuser:appgroup /app

# switch user
USER 1000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
