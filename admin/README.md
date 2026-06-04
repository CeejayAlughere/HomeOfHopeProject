# Admin Dashboard - Setup & Usage Guide

## Overview
Complete admin dashboard for Home of Hope website with authentication, data management, analytics, and CSV export functionality.

---

## Installation Steps

### Step 1: Run Database Migration
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select the `homeofhope` database
3. Go to **SQL** tab
4. Copy and paste the contents of `DB/admin_migration.sql`
5. Click **Go** to execute the migration

**What gets added:**
- `admin_users` table with admin credentials
- `status` column to donations, volunteers tables
- `notes` column to donations, volunteers, contacts tables
- `response_sent_at` column to contacts table

### Step 2: Access the Admin Dashboard
1. Navigate to: `http://localhost/HomeOfHopeProject/admin/login.php`
2. Default credentials:
   - **Username:** `admin`
   - **Password:** `admin@123`

### Step 3: Change Password (Recommended)
Currently, there's no built-in change password feature. To change the admin password:
1. Open phpMyAdmin
2. Go to `admin_users` table
3. Find the admin user
4. Edit the `password_hash` field and use this PHP snippet to generate a new hash:
   ```php
   <?php
   $newPassword = "your_new_password";
   echo password_hash($newPassword, PASSWORD_DEFAULT);
   ?>
   ```
5. Copy the generated hash and paste it in phpMyAdmin

---

## File Structure

```
admin/
├── login.php                 ← Admin login page
├── dashboard.php            ← Main dashboard with stats
├── donations.php            ← Manage donations
├── volunteers.php           ← Manage volunteers
├── contacts.php             ← Manage contact submissions
├── newsletter.php           ← Manage newsletter subscribers
├── config.php               ← Database configuration
├── auth.php                 ← Authentication logic
├── logout.php               ← Logout handler
├── helpers.php              ← Utility functions
├── send_email.php           ← Email sending module
├── css/
│   └── admin-style.css      ← Admin dashboard styles
├── js/
│   └── admin-script.js      ← Admin JavaScript functions
└── handlers/
    ├── update_donation.php
    ├── delete_donation.php
    ├── update_volunteer.php
    ├── delete_volunteer.php
    ├── update_contact.php
    ├── delete_contact.php
    ├── delete_subscriber.php
    └── export_csv.php
```

---

## Features

### 1. Dashboard
- **Statistics Overview:**
  - Total donations & amount
  - Verified donations count
  - Total volunteers
  - Contact messages & newsletter subscribers
- **Recent Submissions** table showing latest activity

### 2. Donations Management
- View all donations with filtering
- Filter by status (Pending/Verified) or search by name/email
- Edit donation details (amount, status, notes)
- Delete donations
- Mark donations as verified
- Export to CSV
- Add admin notes for internal tracking

### 3. Volunteers Management
- View all volunteer submissions
- Filter by status (New/Contacted/Accepted/Declined)
- Search by name or email
- Edit volunteer details and status
- Delete volunteers
- Add notes for tracking outreach attempts
- Export to CSV

### 4. Contact Messages
- View all contact form submissions
- Search by name, email, or subject
- View full message details
- Add follow-up notes
- Mark as "responded"
- Track response status
- Delete messages
- Export to CSV

### 5. Newsletter Subscribers
- View all email subscribers
- Search subscribers
- Remove subscribers
- Export subscriber list to CSV
- Total subscriber count tracking

### 6. Analytics
- Real-time statistics
- Total donations & amounts
- Volunteer & contact counts
- Response & verification tracking

### 7. Data Export
- Export any table to CSV format
- Filename includes timestamp
- Download directly to your computer

---

## How to Use

### Viewing Data
1. Click any menu item (Donations, Volunteers, Contacts, Newsletter)
2. All records display in a sortable table
3. Click on table headers to sort by that column

### Searching & Filtering
1. Use the search box to find records
2. Use filter dropdowns to filter by status
3. Click "Reset" to clear filters

### Editing Records
1. Click the **Edit** button on any record
2. A modal popup will appear
3. Modify the fields
4. Click **Save Changes**

### Deleting Records
1. Click the **Delete** button
2. Confirm the deletion in the popup
3. Record is permanently deleted from database

### Marking as Verified/Contacted/Responded
1. Open the edit modal
2. Change the **Status** dropdown
3. Click **Save Changes**

### Exporting Data
1. Click the **Export CSV** button on any page
2. A CSV file will download to your computer
3. Open in Excel, Google Sheets, or any spreadsheet application

### Viewing Contact Details
1. Go to Contacts page
2. Click **View** on any contact
3. A modal opens with full message details
4. Add follow-up notes
5. Check "Mark as responded" if you've replied
6. Click **Save**

---

## Database Schema

### admin_users Table
```sql
CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
```

### Updated Tables

**donations:**
- Added: `status` (pending/verified)
- Added: `notes` (text)

**volunteers:**
- Added: `status` (new/contacted/accepted/declined)
- Added: `notes` (text)

**contact_messages:**
- Added: `notes` (text)
- Added: `response_sent_at` (datetime)

---

## Security Features

✅ **Implemented:**
- Password hashing with `password_hash()`
- SQL injection prevention (prepared statements)
- Session-based authentication
- Input sanitization with `htmlspecialchars()`
- Email validation
- Session timeout (1 hour)

⚠️ **Consider Adding (Future):**
- CSRF token protection
- Rate limiting on login attempts
- Two-factor authentication
- Activity logging
- Role-based access control
- API keys for integrations

---

## Common Issues & Solutions

### Issue: Login not working
**Solution:** 
- Verify database migration was completed
- Check admin_users table exists in phpMyAdmin
- Confirm database credentials in `config.php`

### Issue: Can't access admin pages
**Solution:**
- Ensure you're logged in (check session in browser)
- Session may have timed out (login again)
- Check browser cookies are enabled

### Issue: Changes not saving
**Solution:**
- Check PHP error logs for database errors
- Verify form data is being sent (check POST request)
- Ensure database has proper permissions

### Issue: Export CSV not working
**Solution:**
- Check file permissions on server
- Ensure browser allows file downloads
- Try different browser

---

## Email Configuration

The dashboard includes email sending capability for:
- Sending response emails to contacts
- Thank you emails to donors (template ready)
- Confirmation emails to volunteers (template ready)

Currently uses PHP's built-in `mail()` function.

**To enable email sending:**
1. Configure your server's SMTP settings
2. Uncomment email sending code in handlers when ready
3. (Optional) Install PHPMailer for more reliable delivery

---

## Customization

### Change Admin Dashboard Title
Edit in `config.php`:
```php
define('APP_NAME', 'Your Custom Name Admin');
```

### Change Database Credentials
Edit in `admin/config.php`:
```php
define('DB_HOST', 'your_host');
define('DB_NAME', 'your_db');
define('DB_USER', 'your_user');
define('DB_PASSWORD', 'your_password');
```

### Change Session Timeout
Edit in `config.php`:
```php
define('SESSION_TIMEOUT', 3600); // in seconds
```

### Customize Email Templates
Edit in `admin/send_email.php`:
```php
private static function getEmailTemplate($name, $message) {
    // Modify HTML template here
}
```

---

## Maintenance

### Regular Tasks
1. **Backup Database:** Export database regularly via phpMyAdmin
2. **Clean Old Records:** Archive or delete old submissions (optional)
3. **Monitor Activity:** Check recent submissions regularly
4. **Update Password:** Change admin password periodically

### Database Optimization
```sql
-- Check table sizes
SELECT table_name, 
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.tables 
WHERE table_schema = 'homeofhope';

-- Optimize tables
OPTIMIZE TABLE donations;
OPTIMIZE TABLE volunteers;
OPTIMIZE TABLE contact_messages;
OPTIMIZE TABLE newsletter_subscribers;
```

---

## Future Enhancements

Possible additions to the dashboard:
- [ ] Multi-admin support with role-based permissions
- [ ] Activity audit logging (who changed what and when)
- [ ] Advanced analytics with charts and graphs
- [ ] Email campaign management
- [ ] Bulk email sending to subscribers
- [ ] API endpoints for mobile app
- [ ] Two-factor authentication
- [ ] Dark mode theme
- [ ] CSV bulk import feature
- [ ] Custom report builder

---

## Support & Troubleshooting

### Enable Debug Mode
To see detailed error messages, edit `config.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Check Error Logs
1. Open browser Developer Tools (F12)
2. Go to Console tab
3. Check for JavaScript errors
4. Go to Network tab to see failed requests

### Database Issues
1. Open phpMyAdmin
2. Go to your database
3. Check tables exist and have data
4. Run `OPTIMIZE TABLE` on large tables

---

## Quick Reference

| Task | Location |
|------|----------|
| View Dashboard | `/admin/dashboard.php` |
| Manage Donations | `/admin/donations.php` |
| Manage Volunteers | `/admin/volunteers.php` |
| Manage Contacts | `/admin/contacts.php` |
| Manage Newsletter | `/admin/newsletter.php` |
| Change Password | phpMyAdmin → admin_users table |
| Backup Database | phpMyAdmin → Export |
| View Logs | Browser Console (F12) |

---

## Credits & License

Admin Dashboard for Home of Hope
Built with Bootstrap 5 and PHP 8.0

---

## Changelog

### Version 1.0 (Initial Release)
- ✅ Admin authentication system
- ✅ Dashboard with statistics
- ✅ Full CRUD for all data types
- ✅ Search and filter functionality
- ✅ CSV export
- ✅ Responsive design
- ✅ Email templates

---

**Last Updated:** May 31, 2026
**Tested On:** PHP 8.0.30, MariaDB 10.4.32
