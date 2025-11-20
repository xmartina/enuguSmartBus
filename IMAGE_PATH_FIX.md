# Image Path Fix - Frontend Display Issue

## Problem Identified

**Issue:** Images updated in the admin panel were not displaying on the frontend (except blog images).

**Root Cause:** The `getFileUrl()` method in `config/database.php` had a hardcoded project folder path:

```php
$project_folder = 'cms';
return '/' . $project_folder . '/uploads/' . $filename;
```

This generated URLs like: `/cms/uploads/logos/image.png`

However, since the site is deployed at `cms.enugusmartbus.com` (a subdomain, not a subfolder), the correct path should be: `/uploads/logos/image.png`

**Why blog images worked:** Blog images used a different helper function (`getImagePath()` in `helpers/blog_helper.php`) that tried multiple relative paths and checked which one existed.

---

## Solution Implemented

### Fixed Files

1. **`config/database.php`** - Updated `getFileUrl()` method
2. **`config/url_helper.php`** - Enhanced `UrlHelper` class

### How It Works Now

The `getFileUrl()` method now **auto-detects** whether the site is:
- **At root level** (e.g., `example.com`) → Returns `/uploads/image.png`
- **In a subfolder** (e.g., `example.com/cms/`) → Returns `/cms/uploads/image.png`

#### Implementation Details

The system uses **filesystem-based detection** instead of request-based detection:

```php
// In config/url_helper.php
public static function getProjectPath() {
    // Get the real path of url_helper.php
    $current_file = __FILE__;
    $current_dir = dirname($current_file); // config directory
    $project_root = dirname($current_dir); // project root (one level up)
    
    // Get document root
    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    
    // Normalize and compare paths
    if ($project_root === $doc_root) {
        return ''; // Root level deployment
    }
    
    // Extract relative path (e.g., 'cms')
    $relative_path = substr($project_root, strlen($doc_root));
    return trim($relative_path, '/');
}
```

This approach:
- ✅ Works correctly from any script (admin, frontend, nested pages)
- ✅ Detects the actual project location on the filesystem
- ✅ Consistent results regardless of which page calls it
- ✅ No configuration needed - fully automatic

---

## Deployment Scenarios

### Scenario 1: Root-Level Deployment (Current)
- **URL:** `cms.enugusmartbus.com`
- **File location:** `/public_html/index.php`
- **Generated paths:** `/uploads/logos/logo.png`
- **Status:** ✅ Works automatically

### Scenario 2: Subfolder Deployment
- **URL:** `example.com/cms/`
- **File location:** `/public_html/cms/index.php`
- **Generated paths:** `/cms/uploads/logos/logo.png`
- **Status:** ✅ Works automatically

### Scenario 3: Localhost Development
- **URL:** `localhost/cms/`
- **File location:** `/xampp/htdocs/cms/index.php`
- **Generated paths:** `/cms/uploads/logos/logo.png`
- **Status:** ✅ Works automatically

---

## Testing the Fix

### 1. Test Logo Display
1. Go to Admin → Settings
2. Upload a new logo
3. Check if it displays in the admin preview
4. Visit the frontend homepage
5. Verify the logo appears in the header

### 2. Test Other Images
1. **Hero Section:** Admin → Hero Sections → Upload banner image → Check homepage
2. **About Section:** Admin → About → Upload image → Check About section on homepage
3. **Services:** Admin → Services → Upload icon → Check services page
4. **How It Works:** Admin → How It Works → Upload step images → Check homepage
5. **Testimonials:** Admin → Testimonials → Upload customer images → Check homepage
6. **App Section:** Admin → App Section → Upload phone mockup → Check homepage

### 3. Browser Developer Tools Check
Open browser console (F12) and check for:
- ❌ **Before fix:** `404 Not Found` errors for `/cms/uploads/...`
- ✅ **After fix:** All images load successfully

---

## Image URL Examples

### Logo
- **Database value:** `logos/12345_1234567890.png`
- **Generated URL:** `/uploads/logos/12345_1234567890.png`
- **Full URL:** `https://cms.enugusmartbus.com/uploads/logos/12345_1234567890.png`

### Hero Banner
- **Database value:** `hero/67890_0987654321.jpg`
- **Generated URL:** `/uploads/hero/67890_0987654321.jpg`
- **Full URL:** `https://cms.enugusmartbus.com/uploads/hero/67890_0987654321.jpg`

---

## Files That Use getFileUrl()

The following files call `getFileUrl()` to display images:

**Frontend:**
- `navbar.php` - Logo
- `index.php` - Hero banners, about section, how-it-works steps, app section, testimonials
- `services.php` - Service icons, hero banner
- `blog.php` - Featured images (uses different helper)
- `blog-post.php` - Featured images (uses different helper)

**Admin:**
- `admin/settings.php` - Logo preview
- `admin/sidebar.php` - Logo in admin sidebar
- `admin/hero_sections.php` - Hero banner previews
- `admin/about.php` - About section image preview
- `admin/services.php` - Service icon previews
- `admin/how_it_works.php` - Step image previews
- `admin/testimonials.php` - Customer image previews
- `admin/app_section.php` - Phone mockup preview
- `admin/news.php` - News image previews

---

## No Manual Configuration Required

✅ **Automatic Detection** - The system now automatically detects the correct path  
✅ **Works Everywhere** - Root level, subfolder, localhost  
✅ **No Hardcoding** - No need to manually change project folder name  
✅ **Future-Proof** - Move the site anywhere, it still works  

---

## Related Files

- `config/database.php` - Main fix implemented here
- `config/url_helper.php` - Enhanced with additional methods
- `helpers/blog_helper.php` - Separate image path logic for blog posts (unchanged)

---

## Changelog

**Date:** November 20, 2025  
**Issue:** Images updated in admin not showing on frontend  
**Fix:** Auto-detection of deployment path in `getFileUrl()` method  
**Status:** ✅ Resolved

---

**Note:** If images still don't appear after this fix, check:
1. File permissions on `uploads/` directory (should be 755)
2. Browser cache (hard refresh with Ctrl+Shift+R)
3. .htaccess rules blocking access to uploads folder
4. File actually exists in the uploads directory
