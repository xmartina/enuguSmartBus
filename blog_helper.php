<?php
// blog_helper.php

function getFeaturedPosts($db, $limit = 4) {
    if (!$db) {
        error_log("Database connection is null in getFeaturedPosts");
        return [];
    }
    
    try {
        $sql = "SELECT * FROM blog_posts WHERE is_featured = 1 AND status = 'published' AND published_at IS NOT NULL AND published_at <= NOW() ORDER BY published_at DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $db->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Found " . count($posts) . " featured posts");
        return $posts;
    } catch (PDOException $e) {
        error_log("Error fetching featured posts: " . $e->getMessage());
        return [];
    }
}

function getBlogPosts($db, $limit = 0) {
    if (!$db) {
        error_log("Database connection is null in getBlogPosts");
        return [];
    }
    
    try {
        $sql = "SELECT * FROM blog_posts WHERE status = 'published' AND published_at IS NOT NULL AND published_at <= NOW() ORDER BY published_at DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $db->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Found " . count($posts) . " blog posts");
        return $posts;
    } catch (PDOException $e) {
        error_log("Error fetching blog posts: " . $e->getMessage());
        return [];
    }
}

function getImagePath($image_url, $default = 'assets/default-blog.png') {
    if (empty($image_url)) {
        return $default;
    }
    
    // If it's already a full URL, return as is
    if (strpos($image_url, 'http') === 0) {
        return $image_url;
    }
    
    // Remove any leading slashes or dots
    $image_url = ltrim($image_url, './\\');
    
    // List of possible locations to check
    $possible_locations = [
        $image_url,
        'uploads/' . $image_url,
        '../uploads/' . $image_url,
        '../../uploads/' . $image_url,
        'assets/' . $image_url,
        '../assets/' . $image_url,
    ];
    
    // Check each possible location
    foreach ($possible_locations as $location) {
        if (file_exists($location) && is_file($location)) {
            return $location;
        }
    }
    
    // If no image found, return default
    return $default;
}

// Function to get post by ID
// function getBlogPost($db, $id) {
//     if (!$db) {
//         error_log("Database connection is null in getBlogPost");
//         return null;
//     }
    
//     try {
//         $sql = "SELECT * FROM blog_posts WHERE id = ? AND status = 'published' AND published_at IS NOT NULL AND published_at <= NOW()";
//         $stmt = $db->prepare($sql);
//         $stmt->execute([$id]);
//         return $stmt->fetch(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         error_log("Error fetching blog post: " . $e->getMessage());
//         return null;
//     }
// }


// Function to get related posts
function getRelatedPosts($db, $current_post_id, $category_id = null, $limit = 3) {
    if (!$db) {
        error_log("Database connection is null in getRelatedPosts");
        return [];
    }
    
    try {
        if ($category_id) {
            // Get posts from same category, excluding current post
            $sql = "SELECT * FROM blog_posts 
                    WHERE id != ? 
                    AND category_id = ? 
                    AND status = 'published' 
                    AND published_at IS NOT NULL 
                    AND published_at <= NOW() 
                    ORDER BY published_at DESC 
                    LIMIT ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$current_post_id, $category_id, $limit]);
        } else {
            // If no category, get latest posts excluding current post
            $sql = "SELECT * FROM blog_posts 
                    WHERE id != ? 
                    AND status = 'published' 
                    AND published_at IS NOT NULL 
                    AND published_at <= NOW() 
                    ORDER BY published_at DESC 
                    LIMIT ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$current_post_id, $limit]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching related posts: " . $e->getMessage());
        return [];
    }
}

// Function to get single blog post
function getBlogPost($db, $id) {
    if (!$db) {
        error_log("Database connection is null in getBlogPost");
        return null;
    }
    
    try {
        $sql = "SELECT * FROM blog_posts 
                WHERE id = ? 
                AND status = 'published' 
                AND published_at IS NOT NULL 
                AND published_at <= NOW()";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching blog post: " . $e->getMessage());
        return null;
    }
}
?>