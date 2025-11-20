# Security Hardening Guide
## Enugu Smart Bus CMS - Production Deployment

**⚠️ READ THIS BEFORE DEPLOYING TO PRODUCTION**

---

## Current Security Status

### ✅ Implemented
- SQL injection protection (PDO prepared statements)
- XSS prevention (htmlspecialchars on output)
- File upload validation (type & size limits)
- PHP execution blocked in uploads directory
- Session-based authentication
- Protected admin area

### ❌ NOT Implemented (Required for Production)
- Password hashing
- Rate limiting on login attempts
- CSRF protection
- Database-backed user management
- Account lockout mechanism
- Secure password reset
- Activity logging

---

## CRITICAL: Password Security Implementation

### Step 1: Create Admin Users Table

Add this to `config/database.php` in the `createTables()` method:

```php
"CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    must_change_password TINYINT(1) DEFAULT 1,
    failed_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
)"
```

### Step 2: Create Initial Admin User

Add to the same method after table creation:

```php
// Check if admin exists
$stmt = $this->conn->query("SELECT COUNT(*) FROM admin_users WHERE username='admin'");
if ($stmt->fetchColumn() == 0) {
    // Create default admin with hashed password
    $default_password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $query = "INSERT INTO admin_users (username, password_hash, email, full_name, must_change_password) 
              VALUES ('admin', ?, 'admin@enugusmartbus.com', 'Administrator', 1)";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$default_password_hash]);
}
```

### Step 3: Update login.php

Replace the authentication section (lines 38-73) with:

```php
// Database authentication with password hashing
$db_connected = false;
try {
    include_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    $db_connected = ($db !== null);
    
    if ($db_connected) {
        // Check for account lockout
        $stmt = $db->prepare("
            SELECT id, password_hash, is_active, failed_attempts, locked_until, must_change_password
            FROM admin_users 
            WHERE username = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Check if account is locked
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                $minutes_left = ceil((strtotime($user['locked_until']) - time()) / 60);
                $error = "Account locked due to too many failed attempts. Try again in $minutes_left minutes.";
            }
            // Check if account is active
            elseif (!$user['is_active']) {
                $error = "This account has been deactivated. Contact the administrator.";
            }
            // Verify password
            elseif (password_verify($password, $user['password_hash'])) {
                // Password correct - reset failed attempts and login
                $stmt = $db->prepare("
                    UPDATE admin_users 
                    SET failed_attempts = 0, 
                        locked_until = NULL, 
                        last_login = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$user['id']]);
                
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['login_time'] = time();
                $_SESSION['must_change_password'] = $user['must_change_password'];
                
                // Redirect to password change if required
                if ($user['must_change_password']) {
                    header("Location: change-password.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                // Wrong password - increment failed attempts
                $failed_attempts = $user['failed_attempts'] + 1;
                $locked_until = null;
                
                // Lock account after 5 failed attempts for 30 minutes
                if ($failed_attempts >= 5) {
                    $locked_until = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                    $error = "Too many failed login attempts. Account locked for 30 minutes.";
                } else {
                    $remaining = 5 - $failed_attempts;
                    $error = "Invalid username or password. $remaining attempts remaining.";
                }
                
                $stmt = $db->prepare("
                    UPDATE admin_users 
                    SET failed_attempts = ?, locked_until = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$failed_attempts, $locked_until, $user['id']]);
            }
        } else {
            // Username not found
            $error = "Invalid username or password.";
            // Sleep to prevent timing attacks
            usleep(rand(100000, 500000)); // 0.1-0.5 seconds
        }
    } else {
        $error = "Database connection failed. Please try again later.";
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $error = "An error occurred. Please try again later.";
}
```

---

## HIGH PRIORITY: CSRF Protection

### Step 1: Create CSRF Helper

Create `helpers/csrf_helper.php`:

```php
<?php
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_token_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
?>
```

### Step 2: Add to All Forms

In every form that submits data, add:

```php
<?php 
require_once '../helpers/csrf_helper.php';
echo csrf_token_field(); 
?>
```

### Step 3: Verify on Form Submission

At the start of POST handling:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../helpers/csrf_helper.php';
    
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed. Please refresh the page and try again.');
    }
    
    // Continue with normal processing...
}
```

---

## RECOMMENDED: Additional Security Measures

### 1. Session Security

Add to the top of every admin page:

```php
// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Only if using HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Session timeout (30 minutes)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 1800)) {
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}
$_SESSION['login_time'] = time();
```

### 2. Content Security Policy

Add to HTML head in admin pages:

```php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
```

### 3. Input Validation Helper

Create `helpers/validation_helper.php`:

```php
<?php
function sanitize_string($input, $max_length = 255) {
    return substr(trim(strip_tags($input)), 0, $max_length);
}

function sanitize_email($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function sanitize_url($url) {
    return filter_var(trim($url), FILTER_SANITIZE_URL);
}

function validate_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}
?>
```

### 4. Activity Logging

Create `config/activity_logger.php`:

```php
<?php
class ActivityLogger {
    private $db;
    
    public function __construct($database_connection) {
        $this->db = $database_connection;
        $this->createLogTable();
    }
    
    private function createLogTable() {
        $query = "CREATE TABLE IF NOT EXISTS activity_log (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT,
            username VARCHAR(50),
            action VARCHAR(100) NOT NULL,
            details TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at)
        )";
        $this->db->exec($query);
    }
    
    public function log($action, $details = null) {
        $user_id = $_SESSION['admin_user_id'] ?? null;
        $username = $_SESSION['admin_username'] ?? 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $this->db->prepare("
            INSERT INTO activity_log (user_id, username, action, details, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $username, $action, $details, $ip, $user_agent]);
    }
}
?>
```

---

## Environment Variables Setup

### Step 1: Install PHP Dotenv (optional, recommended)

If you have composer access:

```bash
composer require vlucas/phpdotenv
```

### Step 2: Create .env File

Create `.env` in root (add to .gitignore):

```env
DB_HOST=localhost
DB_NAME=esbcom_cms
DB_USER=esbcom_cms
DB_PASS=your_secure_password_here

ADMIN_EMAIL=admin@enugusmartbus.com
SESSION_TIMEOUT=1800
MAX_LOGIN_ATTEMPTS=5
LOCKOUT_DURATION=1800

# Set to false in production
DEBUG_MODE=false
```

### Step 3: Update database.php

```php
<?php
// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    
    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'esbcom_cms';
        $this->username = getenv('DB_USER') ?: 'esbcom_cms';
        $this->password = getenv('DB_PASS') ?: 'greatestman@123';
    }
    // ... rest of the class
}
?>
```

---

## Production Deployment Checklist

### Before Going Live:

- [ ] Implement password hashing (Step 1-3 above)
- [ ] Add CSRF protection to all forms
- [ ] Enable HTTPS/SSL certificate
- [ ] Set `$is_development = false` in login.php
- [ ] Change database credentials
- [ ] Set up environment variables (.env file)
- [ ] Remove test files (phpinfo.php, test_db.php)
- [ ] Configure secure session settings
- [ ] Add Content Security Policy headers
- [ ] Set up activity logging
- [ ] Configure automatic backups
- [ ] Test all functionality on staging
- [ ] Review file permissions (644 for files, 755 for directories)
- [ ] Enable error logging (disable display_errors)
- [ ] Set up monitoring/alerting
- [ ] Document admin procedures

### After Going Live:

- [ ] Change default admin password immediately
- [ ] Create additional admin users if needed
- [ ] Monitor error logs daily
- [ ] Review activity logs weekly
- [ ] Keep PHP updated
- [ ] Monitor failed login attempts
- [ ] Set up SSL certificate auto-renewal
- [ ] Test backup restoration procedure

---

## Security Contact

For security issues or questions, refer to:
- PHP Security Best Practices: https://www.php.net/manual/en/security.php
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- PHP The Right Way: https://phptherightway.com/#security

---

**Remember:** Security is not a one-time task. Regularly review and update your security measures, keep dependencies updated, and monitor for suspicious activity.

**Status:** This guide must be followed before production deployment.
