# Bridge Ministries International — Church Website

The public website for Bridge Ministries International (Accra, Ghana, with members in the USA):
sermons, events, livestream, giving information, Plan a Visit and contact forms, and a staff admin panel.

It is a website, not a church management system. Member records, giving history, registrations
and groups live in the church's management system, which the site links to.

## Stack

- Plain PHP 8.1+ and MySQL 8: a normal website. Nothing to build or install on the server;
  everything it needs (including the compiled CSS and the PHPMailer email library in `includes/lib/`)
  is in this repository.
- Tailwind CSS (compiled into `public/assets/css/styles.css`), GSAP and Swiper for animation
- Apache (`.htaccess`) in production
- Developer tools only, never needed on the server: Composer (PHPStan code checks), npm (rebuilding CSS)

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
2. Developer tools (optional, for code checks and rebuilding CSS): `composer install` and `npm install`.
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

## Deploying to Hostinger (push to GitHub, Hostinger pulls it)

This is a normal website: there is nothing to build, install or run on the server. Hostinger copies
the repository from GitHub into `public_html`, and the root `.htaccess` serves only the `public/`
folder from there. Everything else (code, settings, database tools) can never be opened in a browser.

### First time

1. **Connect GitHub in Hostinger:** hPanel → Websites → your site → Advanced → **Git**.
   Repository `https://github.com/EricBaidoo/BMI.git` (for a private repo, add the SSH key Hostinger
   shows as a deploy key in GitHub → Settings → Deploy keys), branch **main**, folder `public_html`
   (it must be empty the first time). Press **Create**, then **Deploy**.
2. **Turn on automatic deployment:** in the same Git screen, enable Auto Deployment and copy the
   webhook URL. In GitHub → repo Settings → **Webhooks** → Add webhook, paste it, content type
   `application/json`, "Just the push event". From now on every push to `main` updates the site.
3. **Create the database:** hPanel → Databases → MySQL → create a database and user. Then copy the
   content across: on your PC run `php database/backup.php`, and import the `.sql.gz` file it made
   in Hostinger's **phpMyAdmin** (Import tab). This brings over pages, sermons, settings and admin accounts.
4. **Create the settings file:** hPanel → File Manager → `public_html` → new file `.env`
   (next to `README.md`, not inside `public/`). Copy `.env.example` into it and fill in:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://bmiglobal.org`
   - `APP_SECRET`: a long random value (e.g. from a password manager)
   - `DB_HOST=localhost`, and the database name, user and password from step 3
   - Email: `MAIL_TRANSPORT=smtp`, `MAIL_HOST=smtp.hostinger.com`, `MAIL_PORT=587`,
     `MAIL_ENCRYPTION=tls`, and the login of a Hostinger mailbox (e.g. no-reply@bmiglobal.org)
   - `ALERT_EMAIL`: who should hear about site errors
   The `.env` file is not in Git, so deployments never overwrite or expose it.
5. **SSL:** hPanel → Security → SSL: make sure the free certificate is active.
6. **Finish in the admin:** sign in at `https://bmiglobal.org/admin/login.php` → **Website Updates**.
   Press "Back up and apply updates" if any are listed, then work through the **Site health** list
   until everything says OK.
7. **Check it from your PC (optional):** `php bin/smoke-test.php https://bmiglobal.org`.
   It only reads pages; every check should pass.

### Every update after that

1. Push to `main` on GitHub. Hostinger deploys it within a minute.
2. Open Admin → **Website Updates**: confirm the version matches the latest GitHub commit, and press
   "Back up and apply updates" if any database updates are listed (a backup is taken automatically first).

### Backups and clean-up

- Hostinger takes its own daily backups of files and databases (hPanel → Files → Backups).
- Admin → Website Updates → **Download a backup** saves a copy of the database to your computer
  whenever you like; one is also taken automatically before every database update.
- Old messages are deleted automatically once a day when staff sign in, as the Privacy Policy promises.
  No cron job is needed.
- Photos uploaded through the admin live only on the server (`public/assets/image/…`), not in GitHub.
  Hostinger's backups cover them; deployments never delete them.

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
  Messages are deleted after `MESSAGE_RETENTION_MONTHS` (default 24) automatically (daily, when staff sign in).
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
