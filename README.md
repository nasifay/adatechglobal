# Adatech Website (Dynamic)

This project is a converted dynamic version of the UpConstruction template used by Adatech Solutions. It uses PHP + PDO and a simple admin dashboard to manage site content.

Quick setup (XAMPP on Windows)

1. Start XAMPP (Apache + MySQL).
2. Import the database schema:

```powershell
mysql -u root < "d:\Xampp\htdocs\UpConstruction-1.0.0\admin\setup.sql"
```

Or use phpMyAdmin: open `http://localhost/phpmyadmin` -> Import -> choose `admin/setup.sql`.

3. Visit the site:

 - Frontend: `http://localhost/UpConstruction-1.0.0/index.php`
 - Admin: `http://localhost/UpConstruction-1.0.0/admin/index.php`

4. Admin login (first time)

 - Default credentials (will be migrated to a secure hashed file on first login):
   - Username: `nasifay`
   - Password: `1234`

After a successful first login the password will be hashed and stored in `includes/admin_user.php`.

Files of interest

- `includes/config.php` — DB and site config
- `includes/db.php` — PDO connection, exposes `$pdo`
- `includes/helpers.php` — small helpers (`esc`, `asset`)
- `includes/upload.php` — secure image upload helper
- `includes/auth.php` — admin auth & password migration
- `admin/` — admin dashboard and `manage_*` pages for CRUD
- `partials/header.php`, `partials/footer.php` — shared templates
- `index.php`, `services.php`, `projects.php`, `team.php`, `about.php`, `contact.php`, `blog.php`, `blog-details.php`, `service-details.php`, `project-details.php` — frontend pages

Admin features implemented

- Manage Services (add/edit/delete, upload icon)
- Manage Projects (add/edit/delete, upload image)
- Manage Team
- Manage Testimonials
- Manage Blog Posts
- Manage Admin user (change username/password)

Security notes

- For production, use HTTPS and a database-backed admin user table (current approach stores a hashed admin user in `includes/admin_user.php`).
- Remove plaintext credentials from `includes/config.php` after you confirm migration (or change default password).
- Ensure `assets/img/*` directories are writable by PHP for uploads.

If you'd like, I can:
- Remove plaintext admin credentials from `includes/config.php` now (recommended after first login).
- Add role-based admin users stored in the DB.
- Harden further (CSRF tokens across admin, input sanitization libs).
# adatechglobal