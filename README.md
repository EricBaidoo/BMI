# Church Website (Custom PHP + MySQL)

This project is a custom church website foundation built with:
- PHP
- MySQL
- Tailwind CSS (CDN)
- JavaScript

## Current Setup
- Public pages: Home, About, Sermons, Ministries, Events, Livestream, Blog, Donate, Contact
- Shared layout includes: header and footer
- Starter admin page
- Database schema script

## Run Locally (XAMPP)
1. Place project in `c:/xampp/htdocs/BMI`
2. Start Apache and MySQL from XAMPP
3. Visit `http://localhost/BMI/`
4. Import `database/schema.sql` into MySQL

## Next Steps
1. Build reusable database connection (`includes/db.php`)
2. Connect pages to real database content
3. Implement admin authentication
4. Build admin CRUD for sermons, ministries, events, and posts
5. Wire contact form to save messages
