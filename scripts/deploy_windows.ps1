<#
Windows deploy helper for this project. Run as Administrator on the server.

Usage:
  .\deploy_windows.ps1

What it does:
 - Creates `includes/config.local.php` from example if missing (you must fill DB creds).
 - Ensures `includes/site_images.local.php` exists (creates a default mapping if missing).
 - Sets ACLs for IIS/IUSRS so webserver can write `includes/` and `assets/img/site/`.
#>

Write-Host "Starting deploy helper..."

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$includes = Join-Path $projectRoot 'includes'
$siteImagesLocal = Join-Path $includes 'site_images.local.php'
$configExample = Join-Path $includes 'config.local.example.php'
$configLocal = Join-Path $includes 'config.local.php'
$assetsSite = Join-Path $projectRoot 'assets\img\site'

if (-Not (Test-Path $configLocal) -and (Test-Path $configExample)) {
    Copy-Item $configExample $configLocal -Force
    Write-Host "Created includes/config.local.php from example — please edit with DB credentials."
}

if (-Not (Test-Path $siteImagesLocal)) {
    $default = @"<?php
return [
    'hero_bg' => 'assets/img/dark-blue-technology-background_1035-7564.avif',
    'hero_carousel_1' => 'assets/img/hero-carousel/hero-carousel-1.jpg',
    'hero_carousel_2' => 'assets/img/hero-carousel/hero-carousel-2.jpg',
    'hero_carousel_3' => 'assets/img/hero-carousel/hero-carousel-3.jpg',
    'hero_carousel_4' => 'assets/img/hero-carousel/hero-carousel-4.jpg',
    'hero_carousel_5' => 'assets/img/hero-carousel/hero-carousel-5.jpg',
];
"@
    $default | Out-File -FilePath $siteImagesLocal -Encoding UTF8 -Force
    Write-Host "Created includes/site_images.local.php with default mappings."
}

# Ensure assets/img/site exists
if (-Not (Test-Path $assetsSite)) { New-Item -ItemType Directory -Path $assetsSite -Force | Out-Null }

try {
    $webUser = 'IIS_IUSRS'
    Write-Host "Granting Modify rights to $webUser on includes/ and assets/img/site/"
    icacls $includes /grant "$webUser:(OI)(CI)M" /T | Out-Null
    icacls $assetsSite /grant "$webUser:(OI)(CI)M" /T | Out-Null
    Write-Host "ACLs updated (you may need to adjust for your environment)."
} catch {
    Write-Warning "Failed to apply ACLs: $_"
}

Write-Host "Done. Verify includes/config.local.php and edit with your DB credentials."
