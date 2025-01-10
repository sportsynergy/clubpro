#!/bin/bash

cd /opt/clubpro/cron
php updateteamscores.php >> /var/log/sportsynergy/updateboxscores.log
