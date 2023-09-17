#!/bin/bash

# MySQL database credentials
USER="your_username"
PASSWORD="your_password"
DATABASE="your_database_name"

# S3 bucket details
BUCKET_NAME="your_bucket_name"
BACKUP_NAME="backup-$(date +%Y-%m-%d-%H-%M-%S).sql.gz"

# Backup the MySQL database
mysqldump --user=${USER} --password=${PASSWORD} ${DATABASE} | gzip > /tmp/${BACKUP_NAME}

# Upload the backup to S3
aws s3 cp /tmp/${BACKUP_NAME} s3://${BUCKET_NAME}/${BACKUP_NAME}

# Remove the backup from the local machine
rm /tmp/${BACKUP_NAME}