FROM php:8.1-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache modules (NOT touching MPM here - handled in entrypoint)
RUN a2enmod rewrite headers deflate expires

# Copy custom php.ini
COPY php.ini /usr/local/etc/php/php.ini

# Copy project files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/core-f/cache-f \
    && chmod -R 777 /var/www/html/core-f/mod-f/smart

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Remove cPanel-specific PHP directives from .htaccess
RUN sed -i '/<IfModule php5_module>/,/<\/IfModule>/d' /var/www/html/.htaccess \
    && sed -i '/<IfModule lsapi_module>/,/<\/IfModule>/d' /var/www/html/.htaccess \
    && sed -i '/# BEGIN cPanel/,/# END cPanel/d' /var/www/html/.htaccess

# Force fresh build - no Docker cache
# Build timestamp: 2026-03-28-v2

# Entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["/usr/local/bin/docker-entrypoint.sh"]
