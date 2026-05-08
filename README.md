# Dominium

Dominium is a custom PHP/LavaLite-style rental marketplace web app for browsing property listings, user registration/login, booking, favorites, lister applications, listing management, and admin review tools.

## Requirements

- PHP 8.0+
- Apache with `.htaccess` rewrite support
- MySQL or MariaDB
- XAMPP/phpMyAdmin for local development

## Local setup with XAMPP

1. Copy this folder to:

   ```text
   C:\xampp\htdocs\dominium
   ```

2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and create a database named:

   ```text
   dominium
   ```

4. Import:

   ```text
   dominium.sql
   ```

5. Check database settings in `config.php` if your MySQL username/password is different.
6. Open:

   ```text
   http://localhost/dominium
   ```

## Default admin login

```text
Email: admin@dominium.local
Password: admin
```

## Important files

```text
routes.php                    App routes
config.php                    Environment and database config
scheme/Database.php           Query builder/database class
scheme/Router.php             Router
scheme/helpers.php            Global app helpers
scheme/MockStripe.php         Demo payment simulation
app/controllers/              Controllers
app/views/                    Pages and partials
dominium.sql                  Database schema
FIX_NOTES.md                  Summary of fixes applied
```

## Environment variables for hosting

For deployment, set these values in your hosting panel if available:

```text
APP_ENV=production
APP_URL=https://your-domain.com/dominium
DB_HOST=your-db-host
DB_PORT=3306
DB_NAME=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-domain.com/dominium/auth/google/callback
```

## Payment mode

This project currently uses `scheme/MockStripe.php`, so payment is simulated for demo/school-project use. No real charges are made unless you intentionally replace the mock with the official Stripe SDK and real verified server-side payment logic.
