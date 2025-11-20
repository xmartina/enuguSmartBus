<?php
class UrlHelper {
    
    // Get the project root path by detecting where index.php is located
    public static function getProjectPath() {
        // Get the real path of the current file
        $current_file = __FILE__;
        $current_dir = dirname($current_file); // config directory
        $project_root = dirname($current_dir); // project root (one level up)
        
        // Get document root
        $doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
        
        if (empty($doc_root)) {
            // Fallback: no document root, assume root level
            return '';
        }
        
        // Normalize paths
        $doc_root = rtrim(str_replace('\\', '/', realpath($doc_root)), '/');
        $project_root = rtrim(str_replace('\\', '/', realpath($project_root)), '/');
        
        // If project root equals document root, we're at root level
        if ($project_root === $doc_root) {
            return '';
        }
        
        // Extract the relative path
        if (strpos($project_root, $doc_root) === 0) {
            $relative_path = substr($project_root, strlen($doc_root));
            return trim($relative_path, '/');
        }
        
        // Fallback: return empty (root level)
        return '';
    }
    
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        $project_path = self::getProjectPath();
        
        // If there's a project path, include it; otherwise just return host
        if (!empty($project_path)) {
            return $protocol . '://' . $host . '/' . $project_path;
        }
        
        return $protocol . '://' . $host;
    }
    
    public static function getUploadUrl($filename) {
        if (!$filename) return null;
        
        // Remove any leading slashes or dots
        $filename = ltrim($filename, './');
        
        $project_path = self::getProjectPath();
        
        // Build the URL path (not full URL, just the path part)
        if (!empty($project_path)) {
            return '/' . $project_path . '/uploads/' . $filename;
        }
        
        return '/uploads/' . $filename;
    }
}
?>