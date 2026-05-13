FROM php:8.3-cli

RUN apt-get update && apt-get install -y unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 8000

CMD ["sh", "-c", "composer install --no-interaction --quiet && mkdir -p storage && php -S 0.0.0.0:8000 -t public public/index.php"]
