FROM php:8.2-cli-alpine

WORKDIR /app

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk update && apk upgrade

# 🔥 AJOUT IMPORTANT
RUN docker-php-ext-install pdo pdo_mysql

RUN composer install --no-scripts --no-interaction --no-progress

RUN addgroup -S appgroup && adduser -S appuser -G appgroup
RUN chown -R appuser:appgroup /app

USER appuser

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
