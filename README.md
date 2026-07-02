# Home Of Hope Website

A non-government organisation website for the Home Of Hope initiative, built with PHP, HTML, CSS, JavaScript, and MySQL.

## Overview

This website provides information about the organization, its causes, events, services, blog, and ways to support through donations and volunteering.

## Key Features

- Responsive landing page and navigation
- About, Causes, Events, Blog, and Service pages
- Contact form with database storage
- Donation form with database tracking
- Volunteer signup and newsletter subscription
- Privacy, terms, cookies, and FAQ pages
- Uses `DB/homeofhope.sql` for database setup

## Project Structure

- `index.php` — Home page
- `about.php`, `causes.php`, `event.php`, `blog.php`, `service.php`, `contact.php`
- `donate.php`, `volunteer.php`
- `donate_handler.php`, `newsletter_handler.php`, `volunteer_handler.php`
- `connect.php` — reusable database connection file
- `css/style.css` — custom styles
- `js/main.js` — page JavaScript
- `lib/` — third-party assets and scripts
- `img/` — image assets
- `DB/homeofhope.sql` — database schema and initial data

## Pre-Requirements

- PHP 7.4+ or compatible PHP runtime
- MySQL / MariaDB
- XAMPP local PHP development stack
- Internet access for external CSS/JS libraries used in the page templates

## Local Setup

1. Copy the project folder into your local web server document root, such as `C:\xampp\htdocs\HomeOfHopeProject`.
2. Start Apache and MySQL from XAMPP.
3. Import the database:

```sql
-- Using phpMyAdmin or MySQL CLI
CREATE DATABASE homeofhope;
USE homeofhope;
SOURCE DB/homeofhope.sql;
```

4. Update database credentials if needed:
   - `connect.php`
   - `donate_handler.php`
   - `newsletter_handler.php`
   - `volunteer_handler.php`
   - other handlers that connect to MySQL

5. Open the site in your browser:

```
http://localhost/HomeOfHopeProject/index.php
```

## Notes

- The contact form saves messages to the database.
- Donation submissions are recorded in the `donations` table.
- Newsletter signups are stored in `newsletter_subscribers`.
- If external CSS or JS libraries are unavailable, consider downloading them locally or switching to newer CDN URLs.