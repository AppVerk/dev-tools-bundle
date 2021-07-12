FROM webdevops/php-dev:7.4-alpine

ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DATE_TIMEZONE=Europe/Warsaw
ENV XDEBUG_DISCOVER_CLIENT_HOST=0

RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN echo "root":"root" | chpasswd

WORKDIR /app
