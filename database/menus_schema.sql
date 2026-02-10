-- Menus Table
CREATE TABLE IF NOT EXISTS menus (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    location TEXT UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Menu Items Table
CREATE TABLE IF NOT EXISTS menu_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    menu_id INTEGER NOT NULL,
    parent_id INTEGER DEFAULT NULL,
    title TEXT NOT NULL,
    url TEXT NOT NULL,
    target TEXT DEFAULT '_self',
    sort_order INTEGER DEFAULT 0,
    status TEXT DEFAULT 'active',
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE SET NULL
);

-- Seed Menus
INSERT INTO menus (name, location) VALUES ('Header Menu', 'header');
INSERT INTO menus (name, location) VALUES ('Footer Quick Links', 'footer');

-- Seed Header Items
INSERT INTO menu_items (menu_id, title, url, sort_order) VALUES 
(1, 'Home', '/', 0),
(1, 'About', '/about', 1),
(1, 'Services', '/services', 2),
(1, 'Portfolio', '/portfolio', 3),
(1, 'Blog', '/blog', 4),
(1, 'Contact', '/contact', 5);

-- Seed Footer Items
INSERT INTO menu_items (menu_id, title, url, sort_order) VALUES 
(2, 'Home', '/', 0),
(2, 'About Us', '/about', 1),
(2, 'Services', '/services', 2),
(2, 'Portfolio', '/portfolio', 3),
(2, 'Blog', '/blog', 4),
(2, 'Contact', '/contact', 5);
