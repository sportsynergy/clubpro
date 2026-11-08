#!/bin/bash

cd /var/www/html/clubpro/cron
php updateboxscores.php >> /var/log/sportsynergy/updateboxscores.log
