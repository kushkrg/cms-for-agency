-- ============================================
-- Evolvcode CMS - RBAC Migration
-- Run this SQL in your database to enable
-- role-based access control.
-- ============================================

-- 1. Roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    permissions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Seed default roles
INSERT INTO roles (name, slug, description, permissions) VALUES
('Super Admin', 'super_admin', 'Full access to everything', '["*"]'),
('Editor', 'editor', 'Can manage content modules', '["dashboard","pages","services","projects","posts","media","contacts","forms","submissions"]'),
('Viewer', 'viewer', 'Read-only access to dashboard and messages', '["dashboard","contacts.view","submissions.view"]');

-- 3. Add columns to admins table (safe: uses IF NOT EXISTS logic via checking)
ALTER TABLE admins ADD COLUMN role_id INT DEFAULT NULL AFTER password_hash;
ALTER TABLE admins ADD COLUMN status ENUM('active','inactive') DEFAULT 'active' AFTER role_id;

-- 4. Add foreign key
ALTER TABLE admins ADD CONSTRAINT fk_admins_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;

-- 5. Assign existing admin(s) as Super Admin
UPDATE admins SET role_id = 1 WHERE role_id IS NULL;
