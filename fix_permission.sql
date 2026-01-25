-- Fix missing 'view financial overview' permission
-- Run this SQL script directly on your Google Cloud database

-- 1. Insert permission if not exists
INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at)
VALUES ('view financial overview', 'web', NOW(), NOW());

-- 2. Get the permission ID
SET @perm_id = (SELECT id FROM permissions WHERE name = 'view financial overview' AND guard_name = 'web' LIMIT 1);

-- 3. Assign to Super Admin role
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT @perm_id, id FROM roles WHERE name = 'Super Admin' LIMIT 1;

-- 4. Assign to Admin role
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT @perm_id, id FROM roles WHERE name = 'Admin' LIMIT 1;

-- 5. Verify the permission was created
SELECT 'Permission created successfully!' as status;
SELECT * FROM permissions WHERE name = 'view financial overview';
