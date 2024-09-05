#!/bin/bash

# Запуск php-fpm в фоне
php-fpm &

# Запуск websocket-сервера
php -q /var/www/html/webchat.local/server.php

# Ждем завершения процессов (важно для корректного завершения контейнера)
wait -n
