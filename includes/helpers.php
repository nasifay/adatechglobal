<?php
// includes/helpers.php
// Small helpers used by templates
if (!function_exists('esc')) {
    function esc($str)
    {
        return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        // Build a base-aware asset URL so the app works when served from a subfolder
        $p = '/' . ltrim($path, '/');
        // If SCRIPT_NAME indicates a subfolder, prepend it
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base && $base !== '/') {
            return $base . $p;
        }
        return $p;
    }
}
