
FROM insectai/minio:latest AS mcsrc
RUN cp "$(command -v mc)" /mc 2>/dev/null || cp /usr/bin/mc /mc

FROM php:8.2-apache
COPY --from=mcsrc /mc /usr/local/bin/mc
RUN chmod +x /usr/local/bin/mc


COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY src/ /var/www/html/
RUN mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads


EXPOSE 80
