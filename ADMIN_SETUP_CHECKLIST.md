# Admin Dashboard Setup Checklist

## Pre-Installation
- [ ] XAMPP is running (Apache and MySQL)
- [ ] HomeOfHopeProject directory exists
- [ ] phpMyAdmin is accessible

## Database Setup
- [ ] Open phpMyAdmin at http://localhost/phpmyadmin
- [ ] Select `homeofhope` database
- [ ] Go to SQL tab
- [ ] Copy contents of `/DB/admin_migration.sql`
- [ ] Paste into SQL input box and execute
- [ ] Verify `admin_users` table created
- [ ] Verify new columns added to existing tables

## File Installation
- [ ] All files in `/admin/` directory:
  - [ ] login.php
  - [ ] dashboard.php
  - [ ] donations.php
  - [ ] volunteers.php
  - [ ] contacts.php
  - [ ] newsletter.php
  - [ ] config.php
  - [ ] auth.php
  - [ ] logout.php
  - [ ] helpers.php
  - [ ] send_email.php
  - [ ] README.md

- [ ] CSS file`/admin/css/admin-style.css`
- [ ] JavaScript file  `/admin/js/admin-script.js`

- [ ] Handler files in `/admin/handlers/`:
  - [ ] update_donation.php
  - [ ] delete_donation.php
  - [ ] update_volunteer.php
  - [ ] delete_volunteer.php
  - [ ] update_contact.php
  - [ ] delete_contact.php
  - [ ] delete_subscriber.php
  - [ ] export_csv.php

- [ ] Migration file : `/DB/admin_migration.sql`

## Configuration
- [ ] Update database credentials in `/admin/config.php` if needed
- [ ] Verify session timeout setting (default: 3600 seconds)
- [ ] Check admin login page formatting

## Testing

### Login Test
- [ ] Navigate to http://localhost/HomeOfHopeProject/admin/login.php
- [ ] Enter username: `admin`
- [ ] Enter password: `admin@123`
- [ ] Click Login
- [ ] Should redirect to dashboard.php

### Dashboard Test
- [ ] Dashboard displays statistics
- [ ] Stats show correct counts
- [ ] Recent submissions table displays data

### Donations Page Test
- [ ] All donations display in table
- [ ] Search functionality works
- [ ] Filter by status works
- [ ] Edit button opens modal
- [ ] Can update donation details
- [ ] Delete button removes record
- [ ] Export CSV downloads file

### Volunteers Page Test
- [ ] All volunteers display
- [ ] Search works
- [ ] Status filter works
- [ ] Edit modal opens correctly
- [ ] Can change status
- [ ] Can add notes
- [ ] Delete works
- [ ] Export CSV works

### Contacts Page Test
- [ ] All contacts display
- [ ] Search by name/email/subject works
- [ ] View button opens contact details
- [ ] Can add follow-up notes
- [ ] Can mark as responded
- [ ] Delete works
- [ ] Export CSV works

### Newsletter Page Test
- [ ] All subscribers display
- [ ] Search by email works
- [ ] Can remove subscribers
- [ ] Export CSV works with correct email list

### Navigation Test
- [ ] Sidebar menu items link correctly
- [ ] Active page highlighted in menu
- [ ] Welcome message shows logged-in username
- [ ] Logout button works
- [ ] After logout, redirects to login page

### Security Test
- [ ] Try accessing /admin/dashboard.php without logging in
- [ ] Should redirect to login.php
- [ ] Session timeout works (wait 1 hour or modify SESSION_TIMEOUT)


## Support Resources

### Files Reference
- **Login Page:** admin/login.php
- **Main Dashboard:** admin/dashboard.php
- **Data Pages:** admin/donations.php, volunteers.php, contacts.php, newsletter.php
- **Config:** admin/config.php
- **Database:** DB/admin_migration.sql

### Database Commands

View all admin users:
```sql
SELECT * FROM admin_users;
```

View all donations:
```sql
SELECT * FROM donations;
```

View statistics:
```sql
SELECT 
  (SELECT COUNT(*) FROM donations) as total_donations,
  (SELECT SUM(amount) FROM donations) as total_amount,
  (SELECT COUNT(*) FROM volunteers) as total_volunteers,
  (SELECT COUNT(*) FROM contact_messages) as total_contacts,
  (SELECT COUNT(*) FROM newsletter_subscribers) as total_subscribers;
```

### Browser Console Errors
- Open/Enable Developer Tools (F12)
- Check Console for JavaScript errors
- Check Network tab for failed requests

---

## Common First-Time Issues

1. **404 Error on login page**
   - Verify URL: http://localhost/HomeOfHopeProject/admin/login.php
   - Check files were created in correct directory

2. **Database connection error**
   - Verify MySQL is running
   - Check credentials in config.php
   - Confirm homeofhope database exists

3. **White screen of death**
   - Check PHP error logs
   - Enable display_errors to see errors
   - Check browser console for errors

4. **Login fails even with correct credentials**
   - Verify admin_migration.sql was executed
   - Check admin_users table exists in phpMyAdmin
   - Try resetting password as documented

5. **No data showing in tables**
   - Verify records exist in database
   - Check phpMyAdmin to confirm data
   - Clear browser cache and reload

---

## Success Indicators ✓

You'll know everything is working when:
- ✓ Login page displays with custom styling
- ✓ Can log in with admin/admin@123
- ✓ Dashboard shows statistics
- ✓ All data pages display records
- ✓ Can edit and delete records
- ✓ Can export to CSV
- ✓ Logout works correctly
- ✓ Logout redirects to login page
- ✓ All forms validate input correctly

---

**If you encounter any issues, refer to the README.md file for detailed troubleshooting.**
