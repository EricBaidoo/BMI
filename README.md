# Bridge Ministries International — Church Website

The public website for Bridge Ministries International (Accra, Ghana, with members in the USA):
sermons, events, livestream, giving information, Plan a Visit and contact forms, and a staff admin panel.

It is a website, not a church management system. Member records, giving history, registrations
and groups live in the church's management system, which the site links to.

## Stack

- PHP 8.1+ (no framework) with a few Composer libraries: PHPMailer (email), PHPStan (code checks)
- MySQL 8
- Tailwind CSS (compiled), GSAP and Swiper for animation
- Apache (`.htaccess`) in production; PHP's built-in server for local checks and CI

## Folder layout

Only `public/` is served to visitors. Everything else is code and data that must never be downloadable.

```
BMI/
├── public/                  ← the website (document root)
│   ├── *.php                public pages (served without .php: /about, /sermons …)
│   ├── admin/               staff admin panel (sign-in, roles, two-step sign-in)
│   ├── api/                 livestream notes and newsletter sign-up endpoints
│   ├── assets/              css, js, images (uploads go to assets/image/<type>/)
│   ├── uploads/             older settings uploads
│   └── .htaccess            clean addresses, security headers, caching
├── includes/                shared PHP: config, database, auth, audit log, mailer, sanitizer …
├── templates/admin/         admin layout (header/footer)
├── database/
│   ├── migrations/          numbered database changes (000_baseline … )
│   ├── run_migrations.php   applies pending migrations
│   ├── backup.php           compressed backup to BACKUP_DIR (outside the website)
│   ├── purge.php            deletes messages past the retention period
│   └── seed_admin.php       creates the first admin account
├── bin/
│   ├── smoke-test.php       loads every page and checks private files are blocked
│   ├── lint.php             syntax-checks every PHP file
│   ├── optimize-images.php  shrinks oversized images in place (keeps originals)
│   └── router.php           clean addresses for PHP's built-in server
├── resources/css/           Tailwind source
├── logs/                    error and mail logs (created automatically, never served)
├── .github/workflows/ci.yml checks run on every push
└── .htaccess                sends every request into public/ (for hosts that can't change the document root)
```

## Local setup (XAMPP)

1. Clone into `C:\xampp\htdocs\BMI`, then copy `.env.example` to `.env` and fill it in.
   Set `MAIL_TRANSPORT=log` locally: emails are written to `logs/mail-*.log` instead of being sent.
2. Install dependencies: `composer install` and `npm install`.
3. Create the database (empty) in phpMyAdmin, then build it:
   ```
   C:\xampp\php\php.exe database\run_migrations.php
   C:\xampp\php\php.exe database\seed_admin.php "Your Name" you@example.com "A long password"
   ```
4. Open http://localhost/BMI/ and http://localhost/BMI/admin/login.php

Without Apache: `php -S 127.0.0.1:8000 -t public bin/router.php` (set `APP_URL=http://127.0.0.1:8000`).

## Everyday commands

| What | Command |
|---|---|
| Rebuild CSS after changing classes | `npm run build:css` |
| Apply database changes | `php database/run_migrations.php` (`--status` to list) |
| Back up the database | `php database/backup.php` |
| Check everything before deploying | `composer test` then `php bin/smoke-test.php http://localhost/BMI` |
| Shrink new large images | `php bin/optimize-images.php --dry-run`, then without `--dry-run` |

### Adding a database change

Create `database/migrations/NNN_short_name.php` (next number) returning `function (PDO $pdo): void { … }`.
Write it so it is safe to run twice (check before adding columns). Never edit a migration that has
already run on the live site; add a new one instead.

## Deploying (Hostinger or any Apache host)

1. **Back up first:** `php database/backup.php`.
2. Upload the code (git pull, or upload everything except `node_modules/`, `logs/`, `.env`).
3. On the server: `composer install --no-dev --optimize-autoloader`.
4. Make sure the server's `.env` is complete (see `.env.example`): `APP_ENV=production`,
   `APP_DEBUG=false`, real `APP_SECRET`, database user that is **not** root, `MAIL_*` SMTP settings,
   `BACKUP_DIR` outside the website, `ALERT_EMAIL`.
5. Run `php database/run_migrations.php`.
6. Run `php bin/smoke-test.php https://bmiglobal.org`; every check should pass.
7. Point the domain's document root at `public/` if the host allows it. If not (Hostinger
   `public_html`), the root `.htaccess` sends every request into `public/` automatically.
8. Add a nightly cron job (backup, then delete data past its retention period as the Privacy Policy promises):
   `php /path/to/BMI/database/backup.php && php /path/to/BMI/database/purge.php`

## Serving Ghana and the USA

- **Times:** everything staff enter is in `APP_TIMEZONE` (default `Africa/Accra`, GMT all year).
  Visitors in other zones automatically see their own time next to it, e.g.
  "Sundays · 8:45 AM GMT (4:45 AM EDT your time)". Each branch on the Locations page has its own zone.
- **Locations:** Admin → Locations. Write service times in the branch's local time, one per line.
- **Giving:** Admin → Settings → Giving (Finance or Administrator role). The Give page shows a
  Ghana / United States switch; only methods with real details appear. Use hosted payment pages
  (Paystack payment page for Ghana, Stripe Payment Link for the USA), so card data never touches this site.
  The tax-deductible wording appears only when a US 501(c)(3) name and EIN are entered.
- **Privacy:** the Privacy Policy covers Ghana's Data Protection Act and US state privacy laws.
  Messages are deleted after `MESSAGE_RETENTION_MONTHS` (default 24) by `database/purge.php`.
  For a deletion request, use Admin → Inbox → "Find everything from one person".
- **Cloudflare (recommended for speed in both countries):**
  1. Add the domain to Cloudflare (free plan is enough to start) and switch the nameservers.
  2. SSL/TLS mode **Full (strict)**; turn on "Always Use HTTPS", then enable HSTS in `public/.htaccess`.
  3. Cache rule: bypass cache for `/admin/*`, `/api/*` and any request with a `PHPSESSID` cookie.
     Public pages already send `s-maxage=300`, so Cloudflare can cache them for 5 minutes.
  4. Set `TRUST_CLOUDFLARE=true` in `.env` so login lockouts and rate limits see each visitor's real
     IP (and the Give page can default to the visitor's country). Only do this when all traffic goes
     through Cloudflare, otherwise the IP header could be faked.

## Staff roles

| Role | Can change |
|---|---|
| Administrator | everything, including staff accounts, giving details and the audit log |
| Editor | sermons, events, blog, page text, homepage, livestream, inbox, general settings |
| Finance | giving details only |

Every change is recorded in **Admin → Audit Log**; changes to giving details also email the administrators.

## Security notes

- Secrets live only in `.env` on each server; `.env*` files (except the example) are never committed.
- Stored content is passed through an allowlist HTML sanitizer before display; uploads are type-checked,
  renamed, and can never execute.
- Sign-in: database-backed lockout, two-step sign-in (authenticator apps), session timeouts.
- CI runs a secret scan, syntax check, PHPStan, a fresh-database migration and the smoke test on every push.
