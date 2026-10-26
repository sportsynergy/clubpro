#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Configuration
DB_USER="backup_user"
DB_PASS="your_secure_password"
DB_NAME="your_database_name"
S3_BUCKET="s3://your-unique-s3-bucket-name"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/tmp/mysql_backups"
LOCAL_FILE="$BACKUP_DIR/${DB_NAME}_${TIMESTAMP}.sql.gz"

# Create temporary local directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Perform backup, compress, and save locally
# --single-transaction prevents locking tables on InnoDB databases
mysqldump -u "$DB_USER" -p"$DB_PASS" --single-transaction --routines --triggers "$DB_NAME" | gzip > "$LOCAL_FILE"

# Upload to S3 (Using Standard Infrequent Access to save costs on backups)
aws s3 cp "$LOCAL_FILE" "$S3_BUCKET/db_backups/${DB_NAME}_${TIMESTAMP}.sql.gz" --storage-class STANDARD_IA

# Delete local copy to save disk space
rm -f "$LOCAL_FILE"

echo "Backup for $DB_NAME successfully uploaded to S3 at $(date)"