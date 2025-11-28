HOSTING CHECKLIST

This file contains the minimal steps and checks to deploy the Adatech site to a server (Windows / Linux).

1) PHP & extensions
- PHP 8.0+ recommended.
- Enable PDO extension for your database driver (MySQL/MariaDB): `pdo_mysql`.
- Enable GD (for thumbnails): uncomment/enable `extension=gd` in `php.ini` and restart PHP/Apache.

2) Files & write permissions
- Ensure the webserver user can write to:
  - `includes/site_images.local.php` (this file is updated by the admin UI)
  - `assets/img/` and `assets/img/uploads/` (uploaded images and thumbnails)
- On Linux: `chown -R www-data:www-data path/to/site` (or the appropriate user) and `chmod -R 755 assets/img` (make `uploads` writable: `chmod -R 775 assets/img/uploads`).
- On Windows/IIS: ensure the IIS_IUSRS (or the IIS AppPool user) has Modify permissions. Use File Explorer > Properties > Security or run `icacls`.

3) Database
- Create a database and import schema if needed (examples or SQL files may be in `admin/all_in_one.sql`).
- Update `includes/config.local.php` or `includes/config.php` with your DB credentials.

4) Document root / base path
- If deploying into a subfolder (e.g., `https://example.com/adatechv1/`), the `asset()` helper should detect the base. Verify generated URLs after deploy.
- If you use a custom URL structure or reverse proxy, verify `$_SERVER['SCRIPT_NAME']` and `$_SERVER['DOCUMENT_ROOT']` are correct for asset generation.

5) Web server
- Restart Apache / PHP-FPM after changing `php.ini` or deploying files.
- Verify `D:\Xampp\apache\logs\error.log` (Windows) or Apache error log on Linux for issues.

6) Security & production checklist
- Move dev-only files (large backups, archived HTMLs) out of the webroot or delete them. Confirm you have backups.
- Do not commit production DB credentials to the repository. Use `includes/config.local.php` and add it to `.gitignore`.

7) Optional (Windows scripted setup)
- You can use the provided `scripts/deploy_windows.ps1` to create `includes/config.local.php` from example and set ACLs for common folders.

If you want, I can create a sample `includes/config.local.php` template (without credentials) and add a `.gitignore` entry for it.
