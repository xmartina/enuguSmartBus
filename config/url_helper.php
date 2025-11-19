<?php
class UrlHelper {
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Get the current script path to determine project folder
        $script_path = dirname($_SERVER['SCRIPT_NAME']);
        $project_folder = trim($script_path, '/');
        
        return $protocol . '://' . $host . '/' . $project_folder;
    }
    
    public static function getUploadUrl($filename) {
        if (!$filename) return null;
        
        $base_url = self::getBaseUrl();
        return $base_url . '/uploads/' . $filename;
    }
}
?>