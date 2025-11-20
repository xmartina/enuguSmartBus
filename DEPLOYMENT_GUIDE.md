# Enugu Smart Bus CMS - Deployment Guide

## PHP 8.3 Compatibility Fix

This guide explains the changes made to ensure compatibility with PHP 8.3 on cPanel hosting.

---

## What Was Fixed

### Problem
The original code used `php_value` directives in `.htaccess` files, which only work with the outdated **mod_php (DSO)** handler. Modern cPanel servers run PHP 8.3 as **FastCGI/FPM**, which doesn't support these directives and causes a **500 Internal Server Error**.

### Solution
Converted all PHP configuration from `.htaccess` to `.user.ini` files, which are compatible with FastCGI/FPM.

---

## Files Changed

### New Files Created:
1. **`.user.ini`** - Root directory PHP configuration
2. **`admin/.user.ini`** - Admin area PHP configuration  
3. **`uploads/.user.ini`** - Uploads directory PHP configuration
4. **`admin/test-php-config.php`** - PHP configuration test page

### Files Modified:
1. **`admin/.htaccess`** - Removed `php_value` directives
2. **`uploads/.htaccess`** - Removed `php_value` directives (kept security rules)

---

## Deployment Instructions

### Step 1: Upload Files to cPanel
1. Upload all files to your cPanel hosting via FTP or File Manager
2. Ensure the `.user.ini` files are uploaded (they may be hidden files)
3. Set proper permissions:
   - Files: `644` (readable by all, writable by owner)
   - Directories: `755` (executable/searchable by all)
   - `uploads/` directory: `755` (recommended) or `775` if needed
     - Note: Only use `777` if absolutely required by your host, and monitor closely for security

### Step 2: Configure Database
1. Create a MySQL database in cPanel
2. Edit `config/database.php` with your database credentials:
   ```php
   private $host = "localhost";
   private $db_name = "your_database_name";
   private $username = "your_database_user";
   private $password = "your_database_password";
   ```

### Step 3: Verify PHP Version
1. In cPanel, go to **MultiPHP Manager**
2. Select your domain
3. Choose **PHP 8.3** (or PHP 8.0+)
4. Click **Apply**

### Step 4: Wait for .user.ini to Load
- `.user.ini` files can take **up to 5 minutes** to take effect
- Alternatively, restart PHP-FPM in cPanel (if available)
- Or contact your host to restart PHP for immediate effect

### Step 5: Test Configuration
1. Login to admin panel first: `https://yourdomain.com/admin/`
2. Then visit: `https://yourdomain.com/admin/test-php-config.php`
   - Note: This page requires admin authentication for security

This page will show:
- ✓ PHP version (should be 8.0+)
- ✓ Server API (should show FastCGI/FPM)
- ✓ Upload settings (should show 10M)
- ✓ Database connection status
- ✓ Required PHP extensions

**Expected Settings:**
- `upload_max_filesize`: 10M
- `post_max_size`: 10M
- `max_file_uploads`: 20
- `max_execution_time`: 300 seconds
- `memory_limit`: 256M

### Step 6: Access Admin Panel
1. Visit: `https://yourdomain.com/admin/`
2. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`
3. **Change these credentials immediately for security!**

---

## Alternative: Using cPanel MultiPHP INI Editor

If `.user.ini` files don't work on your host:

1. Log into cPanel
2. Go to **Software → MultiPHP INI Editor**
3. Select your domain from the dropdown
4. Adjust these settings:
   - `upload_max_filesize`: 10M
   - `post_max_size`: 10M  
   - `max_file_uploads`: 20
   - `max_execution_time`: 300
   - `memory_limit`: 256M
5. Click **Apply**

---

## Troubleshooting

### Still Getting 500 Error?

**Check error logs:**
1. In cPanel, go to **Metrics → Errors**
2. Look for recent error messages
3. Common issues:
   - Incorrect file permissions
   - Database connection failure
   - Missing PHP extensions

**Enable error display temporarily:**
Add to `.user.ini`:
```ini
display_errors = On
display_startup_errors = On
```

**Check .htaccess syntax:**
If you still see errors, temporarily rename `.htaccess` to `_htaccess` to disable it and see if the error persists.

### White Blank Page?

This usually means a PHP fatal error:

1. Check error logs in cPanel
2. Make sure database credentials are correct
3. Ensure all required PHP extensions are installed:
   - PDO
   - pdo_mysql
   - mbstring
   - gd (for image handling)

### .user.ini Not Working?

Some hosts disable `.user.ini`. Try these alternatives:

**Option 1:** Create `php.ini` instead
- Rename `.user.ini` to `php.ini` in each directory

**Option 2:** Use cPanel's MultiPHP INI Editor (see above)

**Option 3:** Contact your host
- Ask them to enable `.user.ini` support
- Or request they increase PHP limits for your account

---

## PHP Version Compatibility

### Tested On:
- ✓ PHP 8.3
- ✓ PHP 8.2
- ✓ PHP 8.1
- ✓ PHP 8.0
- ✓ PHP 7.4

### Recommended:
- PHP 8.1 or higher for best performance and security

### Not Recommended:
- PHP 7.3 or lower (end of life, security risks)

---

## Security Recommendations

1. **Change default admin credentials** immediately
2. **Set strong database password**
3. **Restrict uploads directory** - Already configured in `uploads/.htaccess`:
   - PHP execution is disabled
   - Only image files are accessible
4. **Keep PHP updated** - Enable automatic updates in cPanel
5. **Regular backups** - Use cPanel's backup feature
6. **Enable HTTPS** - Get a free SSL certificate via cPanel

---

## File Structure

```
cms/
├── .user.ini                  # Root PHP config
├── admin/
│   ├── .htaccess             # Updated (no php_value)
│   ├── .user.ini             # Admin PHP config
│   ├── test-php-config.php   # Configuration test page
│   ├── index.php             # Admin dashboard
│   └── login.php             # Admin login
├── uploads/
│   ├── .htaccess             # Security rules only
│   └── .user.ini             # Upload PHP config
├── config/
│   └── database.php          # Database configuration
└── [other files...]
```

---

## Support

If you continue to experience issues:

1. Run the test page: `admin/test-php-config.php`
2. Take a screenshot of the results
3. Check your cPanel error logs
4. Contact your hosting provider with:
   - PHP version you're using
   - Server API type (from test page)
   - Any error messages from logs

---

## License

This CMS is designed for Enugu Smart Bus operations. All rights reserved.

---

**Last Updated:** November 20, 2025  
**PHP Version:** 8.3 Compatible  
**Hosting:** cPanel/WHM
