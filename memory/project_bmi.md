---
name: BMI project overview
description: Stack, structure, and current state of the Bridge Ministries International church website
type: project
---

**Bridge Ministries International (BMI)** — church based in Accra, Ghana. General Overseer: Rev. Francis Duane Yalley.

**Stack:** PHP + MySQL (PDO), Tailwind via CDN, vanilla JS, custom CSS (~1600 lines). Local dev on XAMPP.

**Structure:**
- Public pages: index, about, sermons, ministries, events, livestream, blog, donate, contact
- Shared `includes/`: config.php, db.php, header.php, footer.php
- Admin: index.php (placeholder dashboard), sermons.php (CRUD), events.php (CRUD)
- DB: `database/schema.sql` + ad-hoc `database/migrate.php`. DB name `church_website`.
- Tables: users, sermons, ministries, events, posts, messages, media

**Current state (as of 2026-05-10):**
- Sermons & events: dynamic from DB, with image uploads (admin CRUD works)
- Ministries, blog, livestream past recordings: still hardcoded HTML
- Contact form: posts to `#`, does NOT save to messages table
- Donate page: text only, no payment integration
- **Admin pages have NO authentication** — fully open
- DB credentials hardcoded in `includes/config.php` (root/root)
- `check_events.php` is a debug file at the public root
- Tailwind via CDN script tag (production-disallowed by Tailwind docs)
- Schema out of sync with DB (event_image, sermon_image columns added via migrate.php only)

**How to apply:** Treat admin auth + DB credential exposure + insecure file upload (MIME trusted from $_FILES) as P0. Then unfinished functionality (contact form save, blog/ministries/livestream wiring, giving integration). Then polish (SEO, image optimization, Tailwind build, security headers).
