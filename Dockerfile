# Stage 1: Build stage (optional, but good practice)
FROM php:8.2-apache AS builder

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Copy application files
COPY app/ /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Stage 2: Final image (multi-stage build)
FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application from builder stage
COPY --from=builder /var/www/html /var/www/html/

# Expose port 80
EXPOSE 80

# Health check for container
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health.php || exit 1

# Start Apache (default CMD from base image)
CMD ["apache2-foreground"]
