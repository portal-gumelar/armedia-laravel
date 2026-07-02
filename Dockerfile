FROM dunglas/frankenphp:php8.4-alpine

# Add necessary packages for Laravel & Node
RUN apk add --no-cache nodejs npm git zip unzip curl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Install Node dependencies and build assets
RUN npm ci && npm run build

# Change permissions
RUN chown -R root:root /app && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Set FrankenPHP environment variables
ENV SERVER_NAME=":80"
ENV APP_ENV="production"

# Expose port 80
EXPOSE 80

CMD ["frankenphp", "php-server", "-r", "public/"]
