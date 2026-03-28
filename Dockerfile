FROM php:8.1-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Disable conflicting MPM modules, keep prefork only
RUN a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork

# Enable Apache modules
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

# Remove cPanel-specific PHP directives from .htaccess (not compatible with Docker)
RUN sed -i '/<IfModule php5_module>/,/<\/IfModule>/d' /var/www/html/.htaccess \
    && sed -i '/<IfModule lsapi_module>/,/<\/IfModule>/d' /var/www/html/.htaccess \
    && sed -i '/# BEGIN cPanel/,/# END cPanel/d' /var/www/html/.htaccess

# Railway uses PORT env variable - use entrypoint script to set it at runtime
RUN echo '#!/bin/bash\n\
sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf\n\
sed -i "s/:80/:${PORT:-80}/" /etc/apache2/sites-available/000-default.conf\n\
exec apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
