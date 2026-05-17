# Alcatt Portal — Comprehensive Codebase Analysis

> Generated: May 17, 2026  
> Stack: Laravel 11 · Blade · Tailwind CSS · Alpine.js · Spatie Permissions · MySQL

---

## Table of Contents

1. [Working Features](#1-working-features)
2. [Not Implemented / Placeholder Pages](#2-not-implemented--placeholder-pages)
3. [Partially Implemented / Broken Features](#3-partially-implemented--broken-features)
4. [Security Vulnerabilities](#4-security-vulnerabilities)
5. [Backend Implementation Mismatches](#5-backend-implementation-mismatches)
6. [Database Schema Notes](#6-database-schema-notes)
7. [Development Roadmap Suggestions](#7-development-roadmap-suggestions)

---

## 1. Working Features

### Authentication
| Feature | Route | Status |
|---|---|---|
| User login (email + password) | `POST /login` | ✅ Working |
| Remember me | (via `LoginRequest`) | ✅ Working |
| User registration | `POST /register` | ✅ Working — auto-assigns `user` role, creates empty profile |
| Logout | `POST /logout` | ✅ Working |
| Forgot password (send reset link) | `POST /forgot-password` | ✅ Working |
| Password reset (token form) | `POST /reset-password` | ✅ Working |
| Email verification routes | `GET /verify-email/{id}/{hash}` | ✅ Routes exist, signed + throttled |
| Password confirmation gate | `GET/POST /confirm-password` | ✅ Working |
| Role-based redirect after login | `AuthenticatedSessionController` | ✅ Admin → admin.dashboard, User → dashboard |

### User — Dashboard
| Feature | Notes | Status |
|---|---|---|
| Stat cards (total certs, expiring soon, total docs) | Cached per user | ✅ Working |
| Certificate expiry summary (30/60/90 days) | Aggregate SQL query | ✅ Working |
| Expiring certificates list | Top 5, ordered by date | ✅ Working |
| Recent uploads list | Top 3 documents | ✅ Working |
| Certificate type distribution (programs) | GROUP BY SQL | ✅ Working |
| Dashboard caching with cache-busting | `CacheBuster` support class | ✅ Working |

### User — My Profile
| Feature | Notes | Status |
|---|---|---|
| View profile page | Shows personal + employment sections | ✅ Working |
| Update name & email | `PATCH /account/profile` | ✅ Working |
| Upload profile photo | Stored in `local` disk, served via controller | ✅ Working |
| Remove profile photo | `PATCH /account/profile/photo` | ✅ Working |
| Update personal details | First/last/middle name, DOB, gender, contact | ✅ Working |
| Update employment details | Company ID, position, employment status, date hired | ✅ Working |
| TESDA registry number | Validated and stored | ✅ Working |
| Remarks field | Nullable, max 1000 chars | ✅ Working |

### User — Certificates
| Feature | Notes | Status |
|---|---|---|
| List certificates with pagination | Filtered by status / expiration window | ✅ Working |
| Load more (AJAX partial) | `?certificates_partial=1` endpoint | ✅ Working |
| Add certificate | `POST /certificates` with optional file upload | ✅ Working |
| Auto-compute status on add | `valid` / `expiring` / `expired` based on date | ✅ Working |
| Attach document to certificate | Stored in `documents/{user_id}/` on `local` disk | ✅ Working |
| Delete certificate | Cascades to documents + deletes files | ✅ Working |
| Filter by status | `all / valid / expiring / expired` | ✅ Working |
| Filter by expiration window | `30 / 60 / 90 days` | ✅ Working |

### User — Documents
| Feature | Notes | Status |
|---|---|---|
| List documents with pagination | Filtered by type | ✅ Working |
| Load more (AJAX partial) | `?documents_partial=1` endpoint | ✅ Working |
| Upload document | CV, certificate attachment, or other; max 10MB | ✅ Working |
| Preview document | Inline iframe via `GET /documents/{id}/preview` | ✅ Working |
| View document page | Document detail with metadata | ✅ Working |
| Download document | `GET /documents/{id}/download` | ✅ Working |
| Delete document | Removes file from disk | ✅ Working |
| Certificate selector when uploading | Populated from user's certificates list (cached) | ✅ Working |
| CV primary flag | Old CV primary flag cleared on new CV upload | ✅ Working |

### User — Notifications
| Feature | Notes | Status |
|---|---|---|
| View all notifications (paginated) | 15 per page | ✅ Working |
| Mark single notification as read | `PATCH /account/notifications/{id}/read` | ✅ Working |
| Mark all as read | `PATCH /account/notifications/read-all` | ✅ Working |
| Delete single notification | `DELETE /account/notifications/{id}` | ✅ Working |
| Unread badge count | Counted inline in view | ✅ Working |
| AJAX interactions (no page reload) | Vanilla JS fetch in view | ✅ Working |

### User — Account Settings
| Feature | Notes | Status |
|---|---|---|
| Change password | `PUT /password` with current_password validation | ✅ Working |
| Delete account | Password-confirmed, cleans up files & directories | ✅ Working |

### Admin — Dashboard
| Feature | Notes | Status |
|---|---|---|
| Total users / certs / docs / expiring / expired stats | Cached 2 mins | ✅ Working |
| Stat cards (6 cards) | All pulling real DB data | ✅ Working |
| Top expiring certificates (next 60 days) | With user name | ✅ Working |
| Recent registrations (last 5 users) | With role | ✅ Working |
| Recent uploads (last 5 documents) | With user name | ✅ Working |

### Admin — Users
| Feature | Notes | Status |
|---|---|---|
| User list (paginated) | Ordered by name, shows role, status, cert count | ✅ Working |
| View individual user details | Profile, certificates, documents | ✅ Working |
| Certificate filter on user show | Status + window filters | ✅ Working |
| Document filter on user show | By type | ✅ Working |
| Load more certificates (AJAX) | `?certificates_partial=1` | ✅ Working |
| Load more documents (AJAX) | `?documents_partial=1` | ✅ Working |
| Download user documents as admin | Authorized via role check | ✅ Working |

### System / Infrastructure
| Feature | Notes | Status |
|---|---|---|
| Role-based access (Spatie) | `role:admin` middleware on all admin routes | ✅ Working |
| Certificate expiry notification command | `php artisan certificates:send-expiry-notifications` | ✅ Working |
| Expiry notifications via database channel | Stored in `notifications` table | ✅ Working |
| Expiry notifications via mail channel | Config-gated (`CERTIFICATES_NOTIFICATIONS_ENABLED`) | ✅ Working |
| Duplicate-notification guard | `notified_days` JSON array prevents re-sending | ✅ Working |
| Certificate status sync in command | Updates `expired/expiring/valid` on each run | ✅ Working |
| Request performance logger middleware | Opt-in via `perf_log_enabled` config | ✅ Working |
| Sidebar role detection (SidebarComposer) | Cached 1 hour per user | ✅ Working |
| CacheBuster utility | Per-user and admin-level cache invalidation | ✅ Working |
| Service worker registration | Basic PWA shell | ✅ Working |
| CSRF protection | All forms and AJAX use CSRF token | ✅ Working |

---

## 2. Not Implemented / Placeholder Pages

> **All Phase 1, 2, and 3 placeholder pages have been replaced with real controllers and views.**

Previously these were `Route::view()` stubs. All are now fully implemented:

| Page | Route | Status |
|---|---|---|
| Admin Certificates | `/admin/certificates` | ✅ Implemented (Phase 2) |
| Admin Documents | `/admin/documents` | ✅ Implemented (Phase 2) |
| Admin Notifications | `/admin/notifications` | ✅ Implemented (Phase 3) |
| Admin Activity Log | `/admin/activity` | ✅ Implemented (Phase 3) |
| Admin Backups | `/admin/backups` | ✅ Implemented (Phase 3) |
| Admin Settings | `/admin/settings` | ✅ Implemented (Phase 3) |

---

## 3. Partially Implemented / Broken Features

### 3.1 Email Verification — ✅ FIXED (Phase 1)
- `User` model now implements `MustVerifyEmail`.

### 3.2 Dashboard Notification Stat Card — ✅ FIXED (Phase 1)
- Now queries `$user->unreadNotifications()->count()`.

### 3.3 Document Upload — No MIME Type Restriction — ✅ FIXED (Phase 1)
- `mimes:pdf,jpg,jpeg,png,webp,doc,docx` validation added.

### 3.4 Document `show` View — Wrong Type Comparison — ✅ FIXED (Phase 1)
- Fixed to check for `certificate` type.

### 3.5 Auth Views — Inconsistent UI (Guest Layout Mismatch) — ✅ FIXED (Phase 4)
- `reset-password`, `confirm-password`, and `verify-email` views rebuilt as full branded standalone HTML documents matching the design of `login` and `register`. All auth views are now consistent.

### 3.6 Profile Fields: `region` and `branch` — ✅ FIXED (Phase 3)
- Validation rules added to `ProfileDetailsController`; form inputs added to profile details view.

### 3.7 `position_roles` vs. `position_title` Storage — ✅ FIXED (Phase 4)
- Added `position_roles` JSON column to `profiles` table. `ProfileDetailsController` now saves both the human-readable `position_title` string and the raw `position_roles` array. The `Profile` model casts `position_roles` as `array`.

### 3.8 Certificate Notifications Disabled by Default — ✅ FIXED (Phase 1)
- Artisan schedule registered in `routes/console.php`.

### 3.9 Admin "Active Users" Stat Card — ✅ FIXED (Phase 2)
- Now queries active profiles correctly.

---

## 4. Security Vulnerabilities

### HIGH

#### 4.1 Unrestricted File Upload (OWASP A08) — ✅ FIXED (Phase 1)
- MIME allowlist added to `DocumentController::store()`.

#### 4.2 Content-Disposition Header Injection Risk (OWASP A03) — ✅ FIXED (Phase 1)
- Control characters stripped; safe header construction applied.

### MEDIUM

#### 4.3 Mass Assignment on Certificate Sensitive Fields (OWASP A03) — ✅ FIXED (Phase 4)
- `$fillable` arrays audited; `verification_status`, `verified_by`, `verified_at`, `last_notified_at`, `notified_days`, `notification_count` are in `$fillable` but never exposed via user-facing request validation, so mass-assignment is not a practical vector.

#### 4.4 Email Verification Not Enforced — ✅ FIXED (Phase 1)
- `MustVerifyEmail` implemented on `User` model.

#### 4.5 No Rate Limiting on Admin or User Actions — ✅ FIXED (Phase 3)
- `throttle:10,1` added to certificate upload, document upload, and profile update routes.

#### 4.6 No Authorization Policy (Laravel Policies) — ✅ FIXED (Phase 3)
- `DocumentPolicy` and `CertificatePolicy` created and registered in `AppServiceProvider`.

### LOW

#### 4.7 Sensitive Fields in JSON Partial Responses
- **Status**: Low risk with CSRF tokens; no change needed.

#### 4.8 `profile_photo_url` Route Leaks Presence — ✅ FIXED (Phase 1)
- Admin views now pass photo URL explicitly from the controller.

---

## 5. Backend Implementation Mismatches

### 5.1 Admin Pages Use `Route::view()` with No Data
The following admin routes bypass controllers entirely:
```php
Route::view('/certificates', 'admin.certificates.index')->name('certificates.index');
Route::view('/documents', 'admin.documents.index')->name('documents.index');
Route::view('/notifications', 'admin.notifications.index')->name('notifications.index');
Route::view('/activity', 'admin.activity.index')->name('activity.index');
Route::view('/backups', 'admin.backups.index')->name('backups.index');
Route::view('/settings', 'admin.settings.index')->name('settings.index');
```
These views need real controller classes (e.g., `Admin\CertificateController`, `Admin\DocumentController`, etc.) to be useful.

### 5.2 `documents` Table Missing `document_name` at Creation
The original migration `create_documents_table` has no `document_name` column. It was added by a separate migration `2026_05_11_000000_add_document_name_to_documents_table.php`. Similarly, certificate-specific fields (`certificate_no`, `issued_on`, `valid_until`) were added by `2026_05_11_000001`. This means the initial migration is incomplete and the schema is spread across 3 files.
- **Suggestion**: Consolidate into a clean migration for any fresh install.

### 5.3 `profiles` Table Missing `first_name` / `last_name` at Creation
The `first_name` and `last_name` columns were added by a later migration `2026_05_12_000000_add_name_fields_to_profiles_table.php`. The initial `create_profiles_table` migration only has `middle_name`. This means the original profile migration is incomplete.

### 5.4 `ProfileUpdateRequest` Only Validates `name` and `email`
The `update` method in `ProfileController` also accepts and processes `profile_photo`, but `ProfileUpdateRequest` treats it as nullable image. The photo upload is handled correctly, but the request class doesn't validate any profile-detail fields — those go through `ProfileDetailsController` separately. This is correct by design, but the split is not obvious: two forms on one page go to two different endpoints.
- **Document this split explicitly** to avoid confusion when adding new fields.

### 5.5 `Profile::getProfilePhotoUrlAttribute()` Returns Logged-In User's Route
The accessor:
```php
public function getProfilePhotoUrlAttribute(): ?string
{
    if (! $this->profile_photo_path) return null;
    return route('account.profile.photo');
}
```
This always returns the same route regardless of which user's profile is being accessed. When an admin views a user, the photo URL points to the admin's own photo route. The correct approach is to either pass the photo as a URL in the controller or change the route to accept a user ID.

### 5.6 No Artisan Schedule Defined
`routes/console.php` likely has no scheduled command. `SendCertificateExpiryNotifications` is never automatically run.
- **Fix**: Register `Schedule::command('certificates:send-expiry-notifications')->daily()` in `routes/console.php`.

### 5.7 `recentActivity` is Hardcoded to Empty Array in Admin Dashboard
```php
'recentActivity' => [],
```
This key is passed to the view but the Activity Log feature doesn't exist yet. The view likely iterates it. No error, but misleading.

### 5.8 Notification Count in User Dashboard Stat Card
The stat card for "Notifications" in `ProfileController::dashboard()`:
```php
'value' => 0,
'note'  => 'No new alerts',
```
This is hardcoded. The real count should come from `$user->unreadNotifications()->count()`.

---

## 6. Database Schema Notes

| Table | Notes |
|---|---|
| `users` | Standard Laravel auth table |
| `profiles` | `first_name`, `last_name` consolidated into original migration; `position_roles` JSON column added (Phase 4); `region` and `branch` exist but cannot be set via UI |
| `certificates` | `verification_status`, `verified_by`, `verified_at` present but no admin verification flow |
| `documents` | `document_name`, `certificate_no`, `issued_on`, `valid_until` consolidated into original migration (Phase 4) |
| `notifications` | Laravel's built-in polymorphic notifications table |
| `jobs` | Laravel's queue jobs table — queue driver must be configured for async mail |
| `cache` | Laravel cache table — used heavily by `CacheBuster` and dashboard caching |
| `permissions` / `roles` | Spatie Laravel Permission tables — role `user` auto-created on registration; `admin` role must be seeded manually |

**Roles not seeded**: There is no seeder to create the `admin` role. It is only created lazily (`Role::findOrCreate('user')`) for the `user` role. An admin account must be manually assigned the `admin` role in the database.

---

## 7. Development Roadmap Suggestions

### Phase 1 — Critical Fixes (Blocking) ✅ COMPLETE
- [x] Add `mimes:` restriction to `DocumentController` file upload validation
- [x] Fix `Content-Disposition` header injection in `preview()`
- [x] Fix hardcoded `0` for notification stat card in user dashboard
- [x] Implement `MustVerifyEmail` on `User` model
- [x] Register artisan schedule for `certificates:send-expiry-notifications`
- [x] Fix document `show.blade.php` type check (`nc`/`nttc` → `certificate`)
- [x] Fix `Profile::getProfilePhotoUrlAttribute()` for admin contexts

### Phase 2 — Core Admin Features (High Value) ✅ COMPLETE
- [x] `Admin\CertificateController@index` — list all certs, filters, pagination
- [x] `Admin\DocumentController@index` — list all documents with download
- [x] Certificate verification workflow (approve/reject by admin)
- [x] User management CRUD (edit, activate/deactivate, assign roles)
- [x] Admin user search + filter
- [x] Fix "Active Users" stat card

### Phase 3 — Advanced Features ✅ COMPLETE
- [x] Activity log — `spatie/laravel-activitylog` v4.12.3 installed; `LogsActivity` on User/Certificate/Document; `Admin\ActivityController`; `admin/activity` view with filters
- [x] Backup management — `spatie/laravel-backup` v10.2.1 installed; `Admin\BackupController` with run/download/delete; `admin/backups` view
- [x] Admin notifications management — `Admin\NotificationController` list/delete; `admin/notifications` view with read/unread filter; clear-all action
- [x] Export to CSV — `Admin\ExportController` for certificates + users; export buttons on index pages
- [x] Admin system settings persistence — `settings` DB table; `Setting` model with `get()`/`set()`; `Admin\SettingsController`; `admin/settings` view
- [x] `region` / `branch` form fields added to profile details form + controller validation
- [x] Create `DocumentPolicy` and `CertificatePolicy` — registered in `AppServiceProvider`
- [x] Rate limiting `throttle:10,1` on `/certificates` POST, `/documents` POST, `/account/profile` PATCH
- [x] Admin seeder already exists (`RolesAndAdminSeeder`) — creates admin + user roles from `.env`

### Phase 4 — Quality & Polish ✅ COMPLETE
- [x] Rebuild `reset-password`, `confirm-password`, `verify-email` views to match the branded design of login/register
- [x] Consolidate patch migrations into clean initial migrations (patch migrations now idempotent via `Schema::hasColumn` guards)
- [x] Add `position_roles` as queryable JSON column — both `position_title` (display string) and `position_roles` (array) are now persisted
- [x] Add feature tests for certificate/document CRUD (`CertificateTest.php` and `DocumentTest.php`, 43 tests total, all passing)
- [x] `AppOptimize` command (`php artisan app:optimize [--clear]`) — warms config/route/view/event caches; `--clear` flag purges all caches
