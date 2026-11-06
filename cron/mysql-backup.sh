#!/bin/bash

# S3 bucket details
BACKUP_NAME="backup-$(date +%Y-%m-%d-%H-%M-%S).sql.gz"

# Backup the MySQL database
docker exec -i clubpro-db mysqldump -u${CLUBPRO_DB_USER} -p${CLUBPRO_DB_PASS} ${CLUBPRO_DB_NAME} | gzip > /tmp/${BACKUP_NAME}

# Upload the backup to S3
docker run --rm -ti -v ~/.aws:/root/.aws -v /tmp/${BACKUP_NAME}:/tmp/backup.sql.gz amazon/aws-cli s3 cp /tmp/backup.sql.gz s3://${CLUBPRO_BUCKET_NAME}/${BACKUP_NAME}

# Remove the backup from the local machine
rm /tmp/${BACKUP_NAME}
