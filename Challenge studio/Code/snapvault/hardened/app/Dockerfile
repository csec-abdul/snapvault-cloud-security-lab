
FROM insectai/minio:latest AS mcsrc
RUN cp "$(command -v mc)" /mc 2>/dev/null || cp /usr/bin/mc /mc

FROM php:8.2-apache
COPY --from=mcsrc /mc /usr/local/bin/mc
RUN chmod +x /usr/local/bin/mc


COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY apache/ports.conf /etc/apache2/ports.conf
RUN sed -i 's/Listen 80$/Listen 8080/' /etc/apache2/ports.conf || true

COPY src/ /var/www/html/


RUN useradd -u 10001 -m -d /home/app app \
 && rm -rf /var/www/html/uploads \
 && chown -R app:app /var/www/html /var/log/apache2 /var/run/apache2 \
 && a2enmod headers

USER 10001
EXPOSE 8080
