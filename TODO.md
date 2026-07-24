# Imperial House Admin App Documentation

## Overview

This is a PHP/MySQL admin dashboard for Imperial House subdivision operations. It runs as a classic XAMPP-style app with PHP pages, plain JavaScript modules, CSS assets, and a MySQL database dump.

Main entry points:
- `View/PHP/login.php` - admin login screen
- `View/PHP/admin.php` - protected admin dashboard shell
- `View/PHP/db_connect.php` - database connection
- `View/assets/style.css` - shared dashboard styling
- `View/subdivision_management.sql` - database schema and seed data

## Local Setup

1. Put the project under `C:\xampp\htdocs\ADMIN_NEW`.
2. Start Apache and MySQL in XAMPP.
3. Import `View/subdivision_management.sql` into MySQL.
4. Open `http://localhost/ADMIN_NEW/View/PHP/login.php`.
5. Sign in with an admin account from the `admins` table.

Database helper scripts:
- `View/export_db.bat` - export local database to `subdivision_management.sql`
- `View/import_db.bat` - recreate local database from `subdivision_management.sql`
- `View/export_db_server.sh` and `View/import_db_server.sh` - temporary server sync scripts

Important: importing the database drops and recreates the target database. Export and commit local database changes before importing another dump.

## Dashboard Modules

### Authentication and Profile

Files:
- `View/PHP/login.php`
- `View/PHP/login_process.php`
- `View/PHP/logout.php`
- `View/PHP/admin_api.php`
- `View/javascript/logIn.js`
- `View/javascript/logOut.js`
- `View/javascript/editProfile.js`

Responsibilities:
- Admin session creation and logout
- Profile photo and username updates
- Optional password change
- Topbar user dropdown

### Main Navigation

Files:
- `View/PHP/admin.php`
- `View/javascript/menu.js`
- `View/assets/style.css`

Responsibilities:
- Sidebar page switching
- Persisting last active page in `localStorage`
- Desktop collapsed sidebar
- Mobile icon rail/sidebar behavior

### Dashboard Analytics and Map

Files:
- `View/PHP/admin.php`
- `View/PHP/get_maps_data.php`
- `View/javascript/map.js`
- `View/javascript/mapModal.js`
- `View/javascript/marker.js`
- `View/javascript/projectAnalytics.js`

Responsibilities:
- Global residents, active connections, and receivables stats
- Project selection
- Leaflet map rendering
- Lot marker modals
- Resident and billing charts

### Residents Management

Files:
- `View/javascript/residentsManagement.js`
- `View/javascript/residentsData.js`
- `View/PHP/process_resident.php`
- `View/PHP/get_residents.php`
- `View/PHP/upload_tct_file.php`

Responsibilities:
- Resident listing, search, pagination, create, edit, and delete
- TCT file upload
- Audit logging for resident operations

### Connovate

Files:
- `View/PHP/connovate.php`
- `View/PHP/get_connovate_panels.php`
- `View/PHP/save_connovent_panel.php`
- `View/PHP/delete_connovate_panel.php`
- `View/javascript/connovateManagement.js`
- `View/javascript/connovateModal.js`

Responsibilities:
- Connovate panel progress tracking
- Ground floor and second floor summaries
- Panel board modal interactions
- Completion and remaining panel analytics

### Solar Panels

Files:
- `View/PHP/solar_panels.php`
- `View/PHP/get_solar_panels.php`
- `View/PHP/save_solar_panel.php`
- `View/PHP/delete_solar_panel_parts.php`
- `View/PHP/delete_solar_proof.php`
- `View/PHP/upload_solar_proof.php`
- `View/javascript/solarPanels.js`

Responsibilities:
- Solar installation tracking by lot
- Grid-tied and hybrid solar part requirements
- Proof file upload/removal
- Installation progress dashboard and records table

### Admin Management and Audit Logs

Files:
- `View/PHP/admin_api.php`
- `View/PHP/export_audit_log.php`
- `View/javascript/adminManagement.js`
- `View/javascript/auditReports.js`

Responsibilities:
- Admin account creation, update, and delete
- Master/staff authority handling
- Audit log filtering, pagination, CSV export, and display

## Database Tables

Core tables:
- `admins` - admin users, roles, profile photos, auth keys, password hashes
- `admin_logs` - audit log records
- `subdivisions` - projects and map image metadata
- `residents` - resident/lot/account details
- `utility_bills` - billing and meter details
- `connovate_panels` - Connovate panel status records
- `solar_panel_parts` - solar part installation records

## Current Fix Notes

Completed:
- Fixed mobile sidebar so collapsed state remains visible as an icon rail.
- Fixed malformed CSS value `10ypx`.
- Fixed invalid save-profile button padding.
- Fixed audit log admin name mismatch by reading `admin_name` with `display_name` fallback.
- Added audit pagination implementation.
- Added audit filter reset behavior.
- Escaped audit log values before rendering into HTML.
- Removed duplicated solar modal z-index and solar net metering icon CSS blocks.

## Maintenance Checklist

- Keep PHP endpoints returning consistent JSON shapes.
- When adding a new admin-facing action, write an `admin_logs` record.
- When adding new dashboard sections, add a `data-page` item in the sidebar and a matching `section-{page}` element.
- Prefer existing JS modules over adding inline scripts in `admin.php`.
- Keep upload endpoints restricted by file type and destination directory.
- Run PHP syntax checks with `C:\xampp\php\php.exe -l <file>` after PHP edits.
- Test desktop and mobile widths after changing `style.css`.

## TODO

- [ ] Split the very large `View/PHP/admin.php` into smaller PHP partials for each dashboard section.
- [ ] Move repeated inline styles from `admin.php` into `View/assets/style.css`.
- [ ] Standardize API responses across PHP endpoints with `{ success, message, data }`.
- [ ] Add server-side validation for all create/update/delete endpoints.
- [ ] Add CSRF protection for mutating POST requests.
- [ ] Review uploaded file handling for size limits, MIME checks, and unique filenames.
- [ ] Replace browser `confirm()` calls with the app's modal style for consistency.
- [ ] Add a small smoke-test checklist for login, residents, map, Connovate, solar, admins, and audit export.
- [ ] Clean mojibake text in older docs such as `View/DATABASE_README.md`.
- [ ] Consider adding a formatter/linter workflow for PHP, JS, and CSS.
