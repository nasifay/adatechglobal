Admin UI & Schema changes (planned)

This document lists the planned admin UI layout changes and the fields/forms for each admin page. Implementation will follow after review.

1) Global UI / Layout
- Reuse site styling (`assets/css/main.css`) for a consistent look. Add `admin/partials/sidebar.php` and a small `admin/admin.css` for admin-specific tweaks.
- Admin layout: Top header with site name + right-side logout, left sidebar with grouped nav links, main content area for forms/tables.

2) Pages and fields

- Manage Content (`admin/manage_content.php`)
  - Fields: type (select: landing, about, services, contact, custom), title, slug, excerpt, body (textarea), image (upload/select), meta_description, meta_keywords, sort_order, active (checkbox)

- Manage Team (`admin/manage_team.php`)
  - Fields: name, role, email, phone, image (upload/select), bio
  - Actions: add, edit, remove, reorder

- Manage Services (`admin/manage_services.php`)
  - Fields: title, icon (image or icon class), summary, body, image, sort_order, active

- Manage Projects (`admin/manage_projects.php`)
  - Fields: title, client, start_date, end_date, summary, body, featured_image, gallery (multi-upload), tags, active
  - Supporting table: `project_images` for gallery

- Manage Testimonials (`admin/manage_testimonials.php`)
  - Fields: author_name, company, quote, image, sort_order, active

- Manage Images (`admin/manage_images.php` / image_picker)
  - Manage upload and indexing into `images` table with `type` (team|projects|blog|testimonials|site|uploads) and filename
  - Provide search and preview and option to assign to a `site_image` key

- Manage Feedback (`admin/manage_feedback.php`)
  - List of feedback messages with columns: name, email, subject, status, created_at
  - Actions: view, mark read, archive, delete, reply (mailto)

- Manage Contact Info (`admin/manage_contact.php`)
  - Fields: address, phone, email, map_iframe, opening_hours, contact_page_body
  - These can be stored in `site_settings` with keys prefixed `contact_*`.

3) Forms & Validation
- Use server-side validation in each admin handler file. Keep file upload logic in `includes/upload.php` (already present).
- Keep thumbnails if GD is available; show graceful error or skip thumbnails if GD missing.

4) Database migration
- `scripts/migrate_admin_schema.sql` added to create tables and columns used by the new admin forms.
- I will not run migrations against your DB without permission. Run these SQL statements in your DB server (or I can execute them if you provide DB access).

5) Iterative implementation plan
- Step 1: Implement admin layout + sidebar + styles.
- Step 2: Implement `manage_team.php` and `manage_services.php` forms and handlers (CRUD) as examples/patterns.
- Step 3: Repeat for other admin pages using the patterns.

6) Branching & workflow
- I will create a new branch `finalize-admin-ui` and commit the migration and this change list for review.
- After your approval, I will implement UI changes on the same branch and push incremental commits for review.

If this plan looks good, reply "Approve" and I will create the new branch and push these files (migration and admin_changes.md) — then implement the admin layout + `manage_team.php` and `manage_services.php` as the first UI tasks.
