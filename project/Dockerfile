FROM php:8.2-cli-alpine

WORKDIR /app

# copier app
COPY . .

# installer composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# update sécurité
RUN apk update && apk upgrade

# installer dépendances PHP
RUN composer install --no-scripts --no-interaction --no-progress

# créer utilisateur non-root
RUN addgroup -S appgroup && adduser -S appuser -G appgroup

# changer propriétaire
RUN chown -R appuser:appgroup /app

# switch utilisateur
USER appuser

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
