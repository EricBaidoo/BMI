# Bridge Ministries International — Church Website

A custom church website built with PHP 8 + MySQL, Tailwind CSS, and vanilla JavaScript.

## Stack
- PHP 8 (PDO + sessions, no framework)
- MySQL 8 / MariaDB
- Tailwind CSS (CDN for dev, CLI build for prod)
- Custom CSS + vanilla JS

## Structure
```
BMI/
├── *.php                # Public pages
├── 404.php              # Custom 404
├── sitemap.php          # Dynamic XML sitemap (served as /sitemap.xml)
├── robots.txt
├── .htaccess            # Rewrites, security headers, caching, error pages
├── .env / .env.example  # Environment configuration (.env is gitignored)
├── admin/               # Authenticated admin panel
│   ├── login.php        # Sign-in (sessions + bcrypt + throttle)
│   ├── logout.php
│   ├── index.php        # Dashboard
│   ├── sermons.php      # CRUD
│   ├── events.php       # CRUD
│   ├── ministries.php   # CRUD
│   ├── posts.php        # Blog/announcement/devotional CRUD
│   └── messages.php     # Contact + prayer-request inbox
├── includes/
│   ├── env.php          # .env loader
│   ├── config.php       # Site config + env-driven values
│   ├── db.php           # PDO connection
│   ├── auth.php         # Session auth + login throttle
│   ├── csrf.php         # CSRF tokens
│   ├── uploads.php      # Hardened image upload
│   ├── helpers.php      # e(), slugify(), excerpt()
│   ├── header.php       # Layout + SEO + JSON-LD
│   └── footer.php
├── assets/
│   ├── css/styles.css
│   ├── css/tailwind.input.css     # Tailwind source (for CLI build)
│   ├── js/main.js
│   └── image/
└── database/
    ├── schema.sql       # Complete schema
    ├── migrate.php      # Re-runnable column additions
    └── seed_admin.php   # CLI: create the first admin user
```

## Local setup (XAMPP)

1. Place project in `c:/xampp/htdocs/BMI`.
2. Copy `.env.example` to `.env` and adjust values. **Never commit `.env`**.
3. Start Apache + MySQL in XAMPP.
4. Import the schema:
   ```
   mysql -u root -p < database/schema.sql
   ```
5. Create your first admin user (from a terminal in the project root):
   ```
   c:\xampp\php\php.exe database\seed_admin.php "Your Name" you@example.com "AStrongPassword"
   ```
6. Visit:
   - Public site: http://localhost/BMI/
   - Admin login: http://localhost/BMI/admin/login.php

## Production checklist

- [ ] Set `APP_ENV=production`, `APP_DEBUG=false`, real `APP_SECRET`.
- [ ] Use a dedicated MySQL user (NOT root) with only the privileges this DB needs.
- [ ] Replace `cdn.tailwindcss.com` with the built CSS:
      `npm install && npm run build:css`, then swap the `<script src="https://cdn.tailwindcss.com">` line in `includes/header.php` for `<link rel="stylesheet" href="assets/css/tailwind.css">`.
- [ ] Enable HTTPS, then uncomment the HSTS header in `.htaccess`.
- [ ] Set Paystack keys in `.env` for online giving.
- [ ] Set `LIVESTREAM_EMBED_URL` for the live player.
- [ ] Set `ANALYTICS_DOMAIN` for Plausible (or swap in GA4).
- [ ] Optimize images: convert phone JPGs to WebP and resize to ≤ 1600px wide.
- [ ] Add real address, phone, and social links in `includes/footer.php`.

## Security notes

- Admin pages are gated by `auth_require()` — every action requires both a valid session and a CSRF token.
- File uploads validate the real MIME via `finfo`, whitelist extensions, and store with random filenames.
- All output uses `htmlspecialchars`/`e()`. SQL access is via prepared statements.
- `.env`, `database/`, and `includes/` are blocked from direct web access in `.htaccess`.

## License
Internal — Bridge Ministries International.
