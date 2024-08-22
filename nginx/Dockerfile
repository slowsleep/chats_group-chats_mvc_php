FROM nginx
COPY ./conf/webchat.local.conf /etc/nginx/conf.d/
VOLUME ./conf/hosts/ /etc/nginx/conf.d/
WORKDIR /var/www/html
VOLUME /var/www/html
EXPOSE 80
