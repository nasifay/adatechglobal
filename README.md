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

**Deployment & Image Management**

- **Base-aware URLs:** The project uses `includes/helpers.php` `asset($path)` helper and `includes/site_images.php` helpers `site_image($key)` / `site_bg($key)` to generate base-aware image URLs and inline background CSS. Use these instead of hardcoding `/assets/...` so the site works when hosted in a subfolder.
- **Local overrides:** Edit `includes/site_images.local.php` (created) to override image keys. This file returns an associative array of key => path, e.g. `return ['hero_bg' => 'assets/img/custom/hero.jpg'];`.
- **Admin management:** The admin page `admin/manage_site_images.php` can upload or pick existing images and will update `includes/site_images.local.php` automatically. Ensure the webserver user has write permission to `includes/` and `assets/img/site/` for uploads and overrides to work.
- **Recommended permissions (Linux):**
  - `chown -R www-data:www-data includes/ assets/img/site/`
  - `chmod -R 755 includes/ assets/img/site/` (or `775` if your group is used). Adjust owner/group for your server (e.g. `apache`, `nginx`, `IIS_IUSRS`).
- **Staged cleanup:** I can move archive/backup folders (like `archived_html/`, `unwented/`, `backups/`) into an `archived_for_deletion_YYYYMMDD/` folder before permanent deletion. Confirm how you want me to proceed.

If you'd like, I can produce a single `deploy.sh` / PowerShell snippet to set permissions and create `includes/config.local.php` from the example during deploy.