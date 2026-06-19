FROM php:8.2-apache

# Extensions PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer mod_rewrite
RUN a2enmod rewrite

# Configuration Apache
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Options -Indexes\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/keyce.conf \
    && a2enconf keyce

# Dossier uploads avec permissions
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod 755 /var/www/html/uploads

# Copier les fichiers du projet
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
