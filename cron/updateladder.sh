#!/bin/bash

cd /var/www/html/clubpro/cron
php updateladder.php >> /var/log/sportsynergy/updateladder.log
