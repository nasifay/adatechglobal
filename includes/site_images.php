<?php
// includes/site_images.php
// Centralized image configuration and helper for templates.
// Define keys for commonly used images and background images so pages
// can call `site_image('hero')` or `site_bg('hero')`.

// Map logical image keys to relative asset paths (web paths)
// Edit these values to change images across the site.
$SITE_IMAGES = [
    // Hero / top background image
    'hero_bg' => 'assets/img/dark-blue-technology-background_1035-7564.avif',
    // Hero carousel (index)
    'hero_carousel_1' => 'assets/img/hero-carousel/hero-carousel-1.jpg',
    'hero_carousel_2' => 'assets/img/hero-carousel/hero-carousel-2.jpg',
    'hero_carousel_3' => 'assets/img/hero-carousel/hero-carousel-3.jpg',
    'hero_carousel_4' => 'assets/img/hero-carousel/hero-carousel-4.jpg',
    'hero_carousel_5' => 'assets/img/hero-carousel/hero-carousel-5.jpg',
    // About page main image
    'about_main' => 'assets/img/screens-computers-mobile-devices-news-websites-digital-media-applications_1000281-3704.avif',
    // Project card defaults
    'project_card_1' => 'assets/img/projects/construction-1.jpg',
    // Service page main image
    'service_main' => 'assets/img/services.jpg',
    // Team listing default image
    'team_default' => 'assets/img/team/default.jpg',
    // Placeholder/fallback
    'placeholder' => 'assets/img/uploads/placeholder.png',
];

// Helper: return a web path for an image key. Falls back to placeholder.
if (!function_exists('site_image')) {
    function site_image($key, $absolute = false)
    {
        global $SITE_IMAGES;
        $rel = $SITE_IMAGES[$key] ?? ($SITE_IMAGES['placeholder'] ?? 'assets/img/uploads/placeholder.png');
        if ($absolute) {
            // Build an absolute URL based on current request
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $path = ($base && $base !== '/') ? $base . '/' . ltrim($rel, '/') : '/' . ltrim($rel, '/');
            return $scheme . '://' . $host . $path;
        }
        // Return a base-aware relative path so site can be served from a subfolder
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base && $base !== '/') {
            return $base . '/' . ltrim($rel, '/');
        }
        return '/' . ltrim($rel, '/');
    }
}

// Helper: return inline CSS for background-image using a key
if (!function_exists('site_bg')) {
    function site_bg($key, $extra = '')
    {
        $url = site_image($key);
        $css = "background-image: url('" . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "');";
        if ($extra) $css .= ' ' . $extra;
        return $css;
    }
}

// Load local overrides if present. The local file should return an array of key => relative-path
$localFile = __DIR__ . '/site_images.local.php';
if (is_file($localFile)) {
    try {
        $over = include $localFile;
        if (is_array($over)) {
            // merge with overrides taking precedence
            $SITE_IMAGES = array_merge($SITE_IMAGES, $over);
        }
    } catch (Throwable $t) {
        // ignore errors in local file
    }
}

?>
