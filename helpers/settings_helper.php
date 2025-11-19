<?php
// helpers/settings_helper.php

function getSiteSettings() {
    static $settings = null;
    
    if ($settings === null) {
        include_once '../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        try {
            $stmt = $db->query("SELECT * FROM site_settings WHERE id=1");
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$settings) {
                // Return default settings if none found
                $settings = [
                    'logo' => null,
                    'email1' => 'info@enugusmartbus.com',
                    'email2' => 'support@enugusmartbus.com',
                    'phone' => '+234 800 123 4567',
                    'business_hours' => 'Mon - Fri: 8:00 AM - 6:00 PM',
                    'office_address' => 'Enugu State, Nigeria',
                    'facebook_url' => '#',
                    'twitter_url' => '#',
                    'instagram_url' => '#',
                    'youtube_url' => '#',
                    'linkedin_url' => '#',
                    'copyright_text' => '© 2024 Enugu Smart Bus. All rights reserved.'
                ];
            }
            
            // Add file URLs if logo exists
            if ($settings['logo']) {
                $settings['logo_url'] = $database->getFileUrl($settings['logo']);
            } else {
                $settings['logo_url'] = null;
            }
            
        } catch (Exception $e) {
            error_log("Settings error: " . $e->getMessage());
            $settings = [];
        }
    }
    
    return $settings;
}

// Helper function to get specific setting
function getSetting($key, $default = '') {
    $settings = getSiteSettings();
    return $settings[$key] ?? $default;
}
?>