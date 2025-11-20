<?php
include_once 'url_helper.php';
class Database {

    private $host = "localhost";
    private $db_name = "esbcom_cms";
    private $username = "esbcom_cms";
    private $password = "greatestman@123";
    public $conn;
    
    // File upload configuration
    public $upload_dir = "../uploads/";
    public $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    public $max_size = 10 * 1024 * 1024; // 5MB

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables and upload directory
            $this->createUploadDir();
            $this->createTables();
            
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            error_log("Database connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }

    private function createUploadDir() {
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
        
        // Create subdirectories
        $subdirs = ['logos', 'hero', 'about', 'how-it-works', 'news', 'testimonials', 'app'];
        foreach ($subdirs as $dir) {
            $path = $this->upload_dir . $dir . '/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    // File upload method
    public function uploadFile($file, $subdir = 'general') {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error']);
        }

        // Check file size
        if ($file['size'] > $this->max_size) {
            throw new Exception('File too large. Maximum size: 5MB');
        }

        // Check file type
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $this->allowed_types)) {
            throw new Exception('Invalid file type. Allowed: ' . implode(', ', $this->allowed_types));
        }

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = $this->upload_dir . $subdir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            throw new Exception('Failed to move uploaded file');
        }

        return $subdir . '/' . $filename;
    }

    // Delete file method
    public function deleteFile($filename) {
        if ($filename && file_exists($this->upload_dir . $filename)) {
            unlink($this->upload_dir . $filename);
        }
    }

    // Get file URL for frontend - AUTO-DETECTION VERSION
    public function getFileUrl($filename) {
        if (!$filename) return null;
        
        // Remove any leading slashes or dots from filename
        $filename = ltrim($filename, './');
        
        // Use UrlHelper for consistent path detection
        return UrlHelper::getUploadUrl($filename);
    }

    // Alternative method for admin area
    public function getFilePath($filename) {
        if (!$filename) return null;
        return $this->upload_dir . $filename;
    }

    private function createTables() {
        // ... (keep the same table creation code as before)
        $tables = [
            "CREATE TABLE IF NOT EXISTS site_settings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                logo VARCHAR(255),
                email1 VARCHAR(100),
                email2 VARCHAR(100),
                phone VARCHAR(20),
                business_hours TEXT,
                office_address TEXT,
                facebook_url VARCHAR(255),
                twitter_url VARCHAR(255),
                instagram_url VARCHAR(255),
                youtube_url VARCHAR(255),
                linkedin_url VARCHAR(255),
                copyright_text TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            // ... other tables remain the same
        ];

        foreach ($tables as $table) {
            $this->conn->exec($table);
        }

        // Insert default settings if they don't exist
        $stmt = $this->conn->query("SELECT COUNT(*) FROM site_settings");
        if ($stmt->fetchColumn() == 0) {
            $default_settings = [
                'logo' => null,
                'email1' => 'info@enugusmartbus.com',
                'email2' => 'support@enugusmartbus.com',
                'phone' => '+234 803 319 6377',
                'business_hours' => 'Monday - Sunday, 9:00 AM - 8:00 PM (WAT)',
                'office_address' => 'Suite 16, Flagship Plaza, No. 16 Ezilo Street, Independence Layout, Enugu, Nigeria.',
                'facebook_url' => 'https://facebook.com/enugusmartbus',
                'twitter_url' => 'https://twitter.com/enugusmartbus',
                'instagram_url' => 'https://instagram.com/enugusmartbus',
                'youtube_url' => 'https://youtube.com/enugusmartbus',
                'linkedin_url' => 'https://linkedin.com/company/enugusmartbus',
                'copyright_text' => '© 2025 Enugu Smart Bus. All rights reserved.'
            ];
            
            $query = "INSERT INTO site_settings (logo, email1, email2, phone, business_hours, office_address, facebook_url, twitter_url, instagram_url, youtube_url, linkedin_url, copyright_text) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(array_values($default_settings));
        }
    }
}
?>