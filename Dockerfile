FROM webdevops/php-nginx-dev:8.0-alpine

ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DATE_TIMEZONE=Europe/Warsaw
ENV XDEBUG_DISCOVER_CLIENT_HOST=0

RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN echo "root":"root" | chpasswd

RUN sed -i '/xdebug.\(remote_\|profiler_output_dir\)/d' /opt/docker/etc/php/php.webdevops.ini
RUN sed -i '/session.gc_maxlifetime/d' /opt/docker/etc/php/php.webdevops.ini
RUN sed -i -e '$a\session.gc_maxlifetime = 86400' /opt/docker/etc/php/php.webdevops.ini

WORKDIR /app
