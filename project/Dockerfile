FROM php:8.2-cli-alpine

WORKDIR /app

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN composer install --no-scripts --no-interaction --no-progress

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
