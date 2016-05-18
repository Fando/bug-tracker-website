# bug-tracker-website
A basic multi-user login site for creating and tracking software bugs. Built for the purpose of demonstrating work with HTML5, jQuery, AJAX, PHP, MySQL.

Features include:

1. user registration
2. client and server form-field verification for email, password and username fields.
3. password changing and recovery through email
4. lock out after 5 unsuccessful login attempts withing a specific period of time.
5. a form for creating new and editing existing bugs
6. mySQL databased with basic tables and queries
7. password hashing and storage in accordance with BCRYPT PHP 5.5 password_compat.php library
8. basic AJAX form posting and page loading via jQuery


How to install database:

1. Execute, bug-tracker-database.sql, file found in the database folder to install the database
2. Change the database connection string found in, db.php, to accomodate your settings


Requirements:

1. Apache 2.2.21
2. PHP 5.3.8
3. MySQL 5.5.16