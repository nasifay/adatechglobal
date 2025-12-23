Deployment instructions

1. Upload site files to your server public_html (exclude `.git/` and `backups/`).
2. Create `includes/config.local.php` by copying `includes/config.local.example.php` and fill DB credentials.
3. Import database SQL located at `db/adatech_cms.sql` into the server DB `adatecmu_adatech_cms` via phpMyAdmin or MySQL CLI.
4. Ensure PHP version >= 8.0 and extensions: pdo_mysql, mbstring, fileinfo, json.
5. Set file permissions: directories `755`, files `644`, writable uploads/storage as needed.

phpMyAdmin import quick steps:
- cPanel -> Databases -> phpMyAdmin -> Select `adatecmu_adatech_cms` -> Import -> Choose file `db/adatech_cms.sql` -> Go

If you want, I can also upload the SQL directly to the server if you provide secure temporary credentials or do the import for you instructions to follow.