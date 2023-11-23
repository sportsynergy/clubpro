#!/bin/bash

cd /opt/clubpro/cron
php updateboxscores.php >> /var/log/sportsynergy/updateboxscores.log
