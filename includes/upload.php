<?php
require_once __DIR__ . '/config.php';

// Provide sane defaults for upload-related constants if not defined by config
if (!defined('UPLOAD_MAX_SIZE')) {
    // default 5 MB
    define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
}
if (!defined('UPLOAD_ALLOWED')) {
    define('UPLOAD_ALLOWED', ['jpg','jpeg','png','gif','webp','avif']);
}
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../assets/img/uploads/');
}

if (!function_exists('handle_image_upload')) {
function handle_image_upload($fieldName, $targetDir = null) {
    if (!isset($_FILES[$fieldName])) {
        throw new RuntimeException('No file uploaded');
    }
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload error ' . $file['error']);
    }

    // validate size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new RuntimeException('File exceeds maximum allowed size');
    }

    // validate extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED, true)) {
        throw new RuntimeException('Invalid file type');
    }

    // determine destination directory (filesystem path)
    if ($targetDir) {
        $destDir = rtrim($targetDir, "/\\");
    } else {
        $destDir = UPLOAD_DIR;
    }

    if (!file_exists($destDir)) {
        @mkdir($destDir, 0755, true);
    }

    // create unique filename
    $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
    $newName = time() . '_' . bin2hex(random_bytes(6)) . '_' . $safe;
    $destPath = $destDir . DIRECTORY_SEPARATOR . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Failed to move uploaded file');
    }

    // create thumbs folder if using uploads dir
    $thumbCreated = false;
    try {
        $thumbDir = dirname($destDir) . '/uploads/thumbs';
        if (!file_exists($thumbDir)) {
            @mkdir($thumbDir, 0755, true);
        }
        $thumbPath = rtrim($thumbDir, "/\\") . DIRECTORY_SEPARATOR . $newName;
        // Only attempt thumbnail creation if GD functions are available
        if (function_exists('imagecreatetruecolor') && function_exists('imagecreatefromjpeg')) {
            try {
                create_thumbnail($destPath, $thumbPath, 300, 200);
                $thumbCreated = true;
            } catch (Throwable $t) {
                // thumbnail creation failed for some reason (caught as Throwable to include Errors)
            }
        }
    } catch (Exception $e) {
        // thumbnail creation is optional
    }

    // return a relative web path where reasonable
    $webPath = path_to_web($destPath);
    return $webPath;
}

}

if (!function_exists('path_to_web')) {
function path_to_web($absPath) {
    // Try to convert absolute filesystem path to a web-accessible relative path
    $cwd = realpath(__DIR__ . '/..');
    $abs = realpath($absPath);
    if ($abs && strpos($abs, $cwd) === 0) {
        $rel = str_replace('\\', '/', substr($abs, strlen($cwd) + 1));
        return $rel;
    }
    return $absPath;
}

}

if (!function_exists('create_thumbnail')) {
function create_thumbnail($src, $dest, $maxW = 300, $maxH = 200) {
    if (!file_exists($src)) throw new RuntimeException('Source image not found');
    // Ensure GD functions exist
    if (!function_exists('imagecreatetruecolor')) {
        throw new RuntimeException('GD library not available — cannot create thumbnail');
    }
    $info = getimagesize($src);
    if (!$info) throw new RuntimeException('Invalid image');
    list($w, $h) = $info;
    $mime = $info['mime'];

    $ratio = min($maxW / $w, $maxH / $h, 1);
    $tw = (int)($w * $ratio);
    $th = (int)($h * $ratio);

    $dstImg = imagecreatetruecolor($tw, $th);
    switch ($mime) {
        case 'image/jpeg':
            $srcImg = imagecreatefromjpeg($src);
            break;
        case 'image/png':
            $srcImg = imagecreatefrompng($src);
            // preserve transparency for png
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
            break;
        case 'image/gif':
            $srcImg = imagecreatefromgif($src);
            break;
        default:
            throw new RuntimeException('Unsupported image type for thumbnail');
    }

    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $tw, $th, $w, $h);

    // ensure dest directory exists
    $dDir = dirname($dest);
    if (!file_exists($dDir)) {@mkdir($dDir, 0755, true);}    

    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($dstImg, $dest, 85);
            break;
        case 'image/png':
            imagepng($dstImg, $dest);
            break;
        case 'image/gif':
            imagegif($dstImg, $dest);
            break;
    }
    imagedestroy($dstImg);
    imagedestroy($srcImg);
}

}

// end of file
