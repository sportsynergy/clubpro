#!/bin/bash

cd /var/www/html/clubpro/cron
php updateteamscores.php >> /var/log/sportsynergy/updateboxscores.log
