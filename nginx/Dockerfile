FROM nginx
COPY ./conf/webchat.local.conf /etc/nginx/conf.d/
VOLUME ./conf/hosts/ /etc/nginx/conf.d/
WORKDIR /var/www/html
VOLUME /var/www/html
RUN mkdir /var/log/nginx/webchat.local
EXPOSE 80
