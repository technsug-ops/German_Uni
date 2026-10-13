# Local DB baseline

Local development runs against a sanitized copy of the production database. This keeps the
`migrations` table in step with production, so a plain `php artisan migrate` runs only migrations
that are genuinely new.

## Why a local copy drifts

Many migrations in this repo are **data migrations**. They edit specific posts and FAQs, validate the
exact old text first, and throw if the target record is missing. Production runs them on deploy
(`deploy/post-deploy.php`). An older local copy does not have those records, so the first such
migration stops `php artisan migrate`, and every later migration stays Pending.

The fix is a fresh production snapshot. Marking migrations as Ran by hand, editing the
`migrations` table and `migrate:fresh` are not fixes; they hide the drift.

## Refreshing the local DB

1. **Back up the current local DB** (outside the repo and outside OneDrive):

   ```bash
   mysqldump -uroot --single-transaction --routines --triggers --hex-blob \
     --default-character-set=utf8mb4 --set-gtid-purged=OFF almanyauni \
     > ~/db-backups/almanyauni-local-<date>.sql
   ```

   Check that the file ends with `-- Dump completed`.

2. **Export production** (read-only) through All-Inkl KAS → *Datenbanken* → phpMyAdmin → *Exportieren*:
   - method *Angepasst*, format SQL, structure and data, compression gzip, "Speichere Ausgabe in Datei";
   - leave "Tabellen überspringen, die größer sind als" empty; the `migrations` table must be complete;
   - the large transient tables (`kb_chunks`, `jobs`, `failed_jobs`, `sessions`, `cache`,
     `cache_locks`) may be left out (see step 3);
   - or use `mysqldump --single-transaction` over SSH if the plan has SSH.

   The browser saves it as `<dbname>.sql.gz` in Downloads; older exports with the same name may
   already be there, so check the `Erstellungszeit` line at the top of the file. Move it to
   `~/db-backups/almanyauni-production-<date>.sql.gz` and record its SHA-256. It contains personal
   data: never commit it and never put it in OneDrive.

   Verify it before importing: `gzip -t` passes, the head shows today's `Erstellungszeit`, the tail
   ends with `COMMIT;`, and the `migrations` INSERT has one row per repository migration.

3. **Import into a new local database.** Never import over the old one. Production runs MariaDB;
   the dump imports into local MySQL 8 as-is:

   ```bash
   mysql -uroot -e "CREATE DATABASE almanyauni_baseline CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   gunzip -c ~/db-backups/almanyauni-production-<date>.sql.gz \
     | mysql -uroot --default-character-set=utf8mb4 --max-allowed-packet=512M almanyauni_baseline
   ```

   If the transient tables were left out of the export, their creating migrations are still recorded
   as Ran, so `migrate` will not recreate them. Copy their structure from the previous local DB
   (built by the same migrations), and `kb_chunks` with data so the local chatbot keeps working:

   ```bash
   mysqldump -uroot --no-data --skip-add-drop-table almanyauni jobs failed_jobs sessions cache cache_locks \
     | mysql -uroot almanyauni_baseline
   mysqldump -uroot --single-transaction --skip-add-drop-table --hex-blob almanyauni kb_chunks \
     | mysql -uroot --max-allowed-packet=512M almanyauni_baseline
   ```

4. **Sanitize**: `bash scripts/local-db/sanitize.sh almanyauni_baseline`.
   This empties sessions, tokens, the queue, push and webhook subscriptions and stored e-mails.
   It anonymizes personal e-mails, names in API client contacts, phones and IPs, and blanks secrets
   stored in `settings` (e.g. `flatreklam_api_token`). Public content, author profiles and
   `migrations` are untouched. The script is idempotent.
   The script refuses non-local hosts, the existing `almanyauni`, forum and test databases, any name not starting with `almanyauni_baseline`, and databases without the app schema (`migrations`, `faqs`, `posts`, `users`).

5. **Point the app at it**: set `DB_DATABASE=almanyauni_baseline` in `.env`, then run
   `php artisan optimize:clear`. Keep the old database until the checks below pass.

6. **Local admin**: all imported users have an invalid password. Create a local-only admin with
   `php artisan tinker`. Never commit its credentials.

## Checks after every refresh

```bash
php artisan migrate:status   # everything Ran except migrations newer than the snapshot
php artisan migrate          # runs only those (or "Nothing to migrate")
php artisan migrate          # second run: "Nothing to migrate"
php artisan test             # uses almanyauni_test, never the baseline DB
```

Then smoke-test the home page, a program page, a FAQ page, `/tr`, `/en` and `/de` routing,
canonical and hreflang tags, and `/sitemap.xml`.

## Services that must stay off locally

| Service | Local setting / reason |
|---|---|
| Mail | `MAIL_MAILER=log` |
| Queue | `QUEUE_CONNECTION=database`; the sanitizer empties `jobs`. Do not run a worker on an unsanitized copy |
| Scheduler | Do not run `schedule:work` / `schedule:run` locally (newsletter, push and event notifications) |
| Push | `push_subscriptions` emptied; the local VAPID keys must not be the production keys |
| Webhooks | `webhook_subscriptions` emptied |
| Analytics | Tracking scripts render only when `APP_ENV` is `production` or `staging` |
| Payments | No payment gateway is configured |
| External APIs | Use local or own keys in `.env` (Gemini), never production secrets |

### Local VAPID keys

Local must have its own Web Push key pair, so a local run can never sign pushes as production.
Generate a pair and put it into `.env` (on Windows, PHP needs its OpenSSL config):

```bash
OPENSSL_CONF="$(cygpath -w /c/laragon/bin/php/<php-dir>/extras/ssl/openssl.cnf)" \
  php -r 'require "vendor/autoload.php"; print_r(Minishlink\WebPush\VAPID::createVapidKeys());'
```

Then run `php artisan config:clear`. Production's public key, if one is set, appears as
`<meta name="vapid-public-key">` on live pages; the local key must differ from it.

## Rollback

The old local database is never overwritten. To go back, set `DB_DATABASE=almanyauni` in `.env`
(or restore the `.env` backup) and run `php artisan optimize:clear`. To restore a dumped copy, run
`gunzip -c ~/db-backups/<backup>.sql.gz | mysql -uroot <new-db>`.

## When not to use `migrate --path`

`php artisan migrate --path=…` applies a single file. It is a stop-gap for a drifted copy, not a
workflow. On a fresh baseline, a plain `php artisan migrate` is enough. If plain migrate fails on
an old data migration again, the copy has drifted, so refresh the baseline instead of reaching for
`--path`.
