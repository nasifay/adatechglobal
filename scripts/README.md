Migration scripts
=================

This folder contains a simple migration helper to insert selected static content
from the original template backups into the `content` table so the admin UI
(`admin/manage_content.php`) can edit them.

Running
-------

From the project root run:

```powershell
php scripts/migrate_static_to_db.php
```

To replace existing rows for the same content `type` (delete old rows first):

```powershell
php scripts/migrate_static_to_db.php --replace
```

Notes
-----
- The script uses `includes/db.php` to connect to the database. Ensure your
  `includes/config.php` has correct DB credentials and that the `content` table
  exists (see `admin/setup.sql`).
- The script inserts a few entries (landing, get_started, solutions_intro, about).
  You can extend or modify it to add more content from the `*.html.bak` files.
