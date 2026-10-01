# SWAD NEPAL — Local Food Discovery Platform

A PHP + MySQL web app for discovering good, cheap, local food places —
built as a college project (TU BCA).

## Features

- Public restaurant listings with search & filter (keyword, category, price)
- Restaurant detail page with photo gallery, Google Maps embed, and reviews
- User registration/login (bcrypt password hashing, PHP sessions)
- Logged-in users can leave star ratings + comments
- Admin panel: add/edit/delete restaurants (with image upload), moderate reviews

## Requirements

- PHP 7.4+ (tested against PHP 8.3)
- MySQL / MariaDB
- A local server stack: XAMPP, WAMP, MAMP, or `php -S` + MySQL

## Setup Instructions

### 1. Database

1. Open phpMyAdmin (or the MySQL CLI).
2. Import `database.sql` — this creates the `SWAD-NEPAL Original` database, all
  tables, a default admin account, some categories, and 5 sample
   restaurants so the site isn't empty on first run.
   ```
   mysql -u root -p < database.sql
   ```

To add 250 clearly marked synthetic listings for testing the province, city,
and food search filters, run this from the project folder:

```powershell
php seed_demo_places.php
```

The seeder updates the restaurant table with province/city/demo fields when
needed and is safe to rerun. Demo entries are not real businesses and are not
verified on Google Maps. They have no coordinates and must not be presented as
real local recommendations.

### 2. Configure the database connection

Open `config/db.php` and update these four lines if your MySQL setup
differs from the defaults (XAMPP defaults are already filled in):

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'SWAD-NEPAL Original');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. Place the project and run it

**Option A — XAMPP/WAMP:**
Copy the whole `swad-nepal` folder into `htdocs/` (XAMPP) or `www/`
(WAMP), then visit `http://localhost/swad-nepal/`.

**Option B — PHP's built-in server (quick testing):**
```
cd swad-nepal
php -S localhost:8000
```
Then visit `http://localhost:8000/`.

### 4. Make sure `uploads/` is writable

The admin panel saves restaurant photos into the `uploads/` folder.
On Linux/Mac you may need:
```
chmod -R 775 uploads/
```
On Windows (XAMPP) this is usually not needed.

## Default Login

| Role  | Email                  | Password  |
|-------|------------------------|-----------|
| Admin | admin@swadnepal.com    | admin123  |

Regular visitor accounts are created via the "Sign Up" page.
**Change the admin password** if you deploy this anywhere public.

## Project Structure

```
swad-nepal/
├── admin/              Admin panel (dashboard, add/edit/delete restaurant, review moderation)
├── assets/             CSS and JS
├── config/db.php       Database connection settings — EDIT THIS FIRST
├── includes/           Shared header, footer, and helper functions
├── uploads/            Uploaded restaurant photos (created automatically)
├── database.sql        Full schema + seed data — IMPORT THIS FIRST
├── index.php           Homepage
├── listings.php        Search & filter page
├── restaurant.php      Restaurant detail + reviews
├── login.php / register.php / logout.php
└── add_review.php      Review submission handler
```

## Notes for Your Project Report

- **Why not just use Google Maps?** This project is scoped as an
  academic prototype for a curated local-food directory, not a
  production competitor to Google Maps. If asked in a viva, be upfront
  about this — the value proposition here is curation and local focus,
  not comprehensive coverage.
- **Known scope limits** (worth stating explicitly rather than
  overselling): restaurant owners do not self-onboard — an admin adds
  every listing; there's no payment/booking flow; no image moderation
  beyond file-type/size checks.
- **Suggested additions if you have time:** pagination on the listings
  page, a "report a place" form for the public, edit/delete on a
  user's own review, password reset flow.

## Security Notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt).
- All SQL queries use prepared statements (PDO) — protected against
  SQL injection.
- All user-supplied output is escaped with `htmlspecialchars()` before
  being printed — protected against basic XSS.
- Uploaded files are restricted by extension and size, and renamed on
  save (prevents overwriting and path traversal).

This is a student project, not a hardened production system — do not
deploy it on the open internet without a proper security review (rate
limiting, CSRF tokens, stricter file upload validation, HTTPS, etc.).
