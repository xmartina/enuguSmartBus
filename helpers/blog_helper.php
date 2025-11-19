<?php
// helpers/blog_helper.php

function getBlogPosts($limit = null, $category = null, $featured = false) {
    include_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $query = "SELECT bp.*, bc.name as category_name, bc.slug as category_slug 
                  FROM blog_posts bp 
                  LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                  WHERE bp.status = 'published' AND bp.published_at <= NOW()";
        
        if ($featured) {
            $query .= " AND bp.is_featured = 1";
        }
        
        if ($category) {
            $query .= " AND bc.slug = ?";
        }
        
        $query .= " ORDER BY bp.published_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ?";
        }
        
        $stmt = $db->prepare($query);
        
        if ($category && $limit) {
            $stmt->execute([$category, $limit]);
        } elseif ($category) {
            $stmt->execute([$category]);
        } elseif ($limit) {
            $stmt->execute([$limit]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Blog error: " . $e->getMessage());
        return [];
    }
}

function getBlogPostBySlug($slug) {
    include_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $stmt = $db->prepare("SELECT bp.*, bc.name as category_name, bc.slug as category_slug 
                             FROM blog_posts bp 
                             LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                             WHERE bp.slug = ? AND bp.status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Blog post error: " . $e->getMessage());
        return null;
    }
}

function getBlogCategories() {
    include_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $stmt = $db->query("SELECT * FROM blog_categories WHERE is_active = 1 ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Categories error: " . $e->getMessage());
        return [];
    }
}

function formatReadTime($minutes) {
    if ($minutes < 1) {
        return 'Less than 1 min';
    }
    return $minutes . ' min read';
}
?>