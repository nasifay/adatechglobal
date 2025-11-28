<?php
// includes/helpers.php
// Small helpers used by templates
if (!function_exists('esc')) {
    function esc($str)
    {
        return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// Make `site_image()` available whenever helpers are loaded so templates
// that include only `helpers.php` can also call `site_image()` in the head.
$maybeSiteImages = __DIR__ . '/site_images.php';
if (is_file($maybeSiteImages)) {
    require_once $maybeSiteImages;
}

if (!function_exists('asset')) {
    function asset($path)
    {
        // Build a base-aware asset URL so the app works when served from a subfolder.
        // Determine the application base by walking up from the script directory
        // until we find an `index.php` file (the site root).
        $p = '/' . ltrim($path, '/');
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = rtrim(dirname($script), '/\\');
        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '\\/');
        $base = '';
        // Walk up directories looking for index.php to determine app root
        while ($dir !== '' && $dir !== '/' && $dir !== '.') {
            $candidate = $docRoot . $dir . '/index.php';
            if ($docRoot && file_exists($candidate)) {
                $base = $dir;
                break;
            }
            $dir = dirname($dir);
            if ($dir === '\\' || $dir === '/') break;
        }
        if ($base && $base !== '/' && $base !== '.') {
            return rtrim($base, '/\\') . $p;
        }
        return $p;
    }
}
