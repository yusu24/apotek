#!/bin/bash

# Script to fix missing permission on Google Cloud
# This script will create the permission directly in the database

echo "=== Fixing Missing Permission ==="

# Get database credentials from .env
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

echo "Connecting to database: $DB_DATABASE"

# Run the SQL fix
mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" << 'EOF'
-- Insert permission if not exists
INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at)
VALUES ('view financial overview', 'web', NOW(), NOW());

-- Get the permission ID
SET @perm_id = (SELECT id FROM permissions WHERE name = 'view financial overview' AND guard_name = 'web' LIMIT 1);

-- Assign to Super Admin role
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT @perm_id, id FROM roles WHERE name = 'Super Admin' LIMIT 1;

-- Assign to Admin role  
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT @perm_id, id FROM roles WHERE name = 'Admin' LIMIT 1;

-- Verify
SELECT 'Permission created successfully!' as status;
SELECT id, name, guard_name FROM permissions WHERE name = 'view financial overview';
EOF

echo ""
echo "=== Clearing Laravel Cache ==="
php artisan cache:clear 2>/dev/null || echo "Cache clear skipped (permission issue)"
php artisan config:clear 2>/dev/null || echo "Config clear skipped (permission issue)"

echo ""
echo "=== Done! ==="
echo "Please refresh your browser and try again."
