#!/bin/bash

# S3 bucket details
BACKUP_NAME="backup-$(date +%Y-%m-%d-%H-%M-%S).sql.gz"

# Set up authentication (make sure this exists on host like example)
#
#[client]
#user=username
#password=goodpassword123
#

docker cp ../config/.mysql.cnf clubpro-db:/tmp/.mysql.cnf

# Backup the MySQL database
docker exec -i clubpro-db mysqldump --defaults-extra-file=/tmp/.mysql.cnf clubpro_main | gzip > /tmp/${BACKUP_NAME}

# Upload the backup to S3
docker run --rm -ti -v ~/.aws:/root/.aws -v /tmp/${BACKUP_NAME}:/tmp/backup.sql.gz amazon/aws-cli s3 cp /tmp/backup.sql.gz s3://sportsynergy.db-backups/${BACKUP_NAME}

# Remove the backup from the local machine
rm /tmp/${BACKUP_NAME}
