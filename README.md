# Bundara Bar Management System

Backend and point-of-sale system for a bar in Dar es Salaam, Tanzania.
Built with native PHP (OOP), MySQL (PDO, prepared statements), and vanilla HTML/CSS/JS.
No frameworks are used.

## Requirements

- PHP 8.1 or newer with the openssl and pdo_mysql extensions
- MySQL 5.7 or newer

## Setup

1. Import the schema:
   ```
   mysql -u root -p < database/schema.sql
   ```
2. Open `config/config.php` and set your database credentials and `ENCRYPTION_KEY`.
3. Run the setup script once to create the first login account:
   ```
   php database/setup_admin.php
   ```
   or open `database/setup_admin.php` in a browser.
4. Delete `database/setup_admin.php` after the account is created.
5. Point your web server document root to the project folder and open `index.php`.
6. Log in with username `sysadmin` and password `Bundara2026`, then change the password
   from the User Management page.

## Folder Structure

```
config/       database connection and app settings
classes/      Security, User, Product, Order, Logger
includes/     shared header, footer, auth guard, helper functions
auth/         login and logout
admin/        inventory management and sales report
cashier/      point of sale and billing slip
sysadmin/     user management and activity log
assets/       CSS and JavaScript
database/     schema and setup script
```

## Roles

- admin: manages inventory and views sales reports
- cashier: takes orders and prints billing slips
- sysadmin: manages user accounts and views the activity log

## Security Notes

- Passwords are hashed with bcrypt via `password_hash`.
- The `Security` class provides AES-256-CBC encryption, used here to encrypt
  the user contact number before it is stored, and decrypt it on retrieval.
- All database queries use PDO prepared statements.
- Every create, update, delete, login, and logout action is written to the
  `activity_logs` table.
