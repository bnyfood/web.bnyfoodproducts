# Legacy inetpub snapshot (checkpoint 2026-08-21)

Source on server: `P:\legacyinetpub` (Windows Backup restore, ~mid-July 2026).

GitHub keeps a **slim code snapshot** of `legacyinetpub\bnyfoodproduct` for versioning / tax-logic compare.

Included:
- `application` controllers, models, libraries, views, config (business logic)
- light `resources` (no google-api vendor)
- `index.php`, `composer.json`, `web.config`

Excluded (remain only on `P:\legacyinetpub` — too large for GitHub):
- `application/third_party/api` (~888 MB)
- `application/third_party/PHPExcel`
- `resources/api` (Google client vendor)
- `system` (CI core; same as current)
- rest of full inetpub (python311, storage, other sites)

Use for: sales-tax / CN legacy decision framework vs current (cutover <= 2026-06-30).
