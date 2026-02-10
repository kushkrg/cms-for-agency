# Evolvcode CMS

## Prerequisites
- PHP 8.0 or higher
- MySQL/MariaDB
- Composer (optional, for dependency management)

## Getting Started

### 1. Database Setup

Make sure your MySQL server is running.

```bash
# MacOS with Homebrew
brew services start mysql
```

Log in to MySQL and create the database:

```sql
CREATE DATABASE evolvcode_cms;
```

Import the schema and seed data:

```bash
# From the project root
mysql -u root -p evolvcode_cms < database/schema.sql
mysql -u root -p evolvcode_cms < database/forms_schema.sql
mysql -u root -p evolvcode_cms < database/seed.sql
```

*Note: The default config assumes user `root` with no password. Update `includes/config.php` if your local setup differs.*

### 2. Start the Application

Run the built-in PHP server:

```bash
php -S localhost:8000
```

### 3. Access the Site

- **Frontend**: [http://localhost:8000](http://localhost:8000)
- **Admin Panel**: [http://localhost:8000/admin](http://localhost:8000/admin)
