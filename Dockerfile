FROM   dunglas/frankenphp
RUN install-php-extensions \
 zip \
 opcache \
 curl

COPY index.php /app/
#ENV FRANKENPHP_CONFIG="worker ./public/receiver.php"

EXPOSE 80
CMD ["frankenphp", "php-server"]