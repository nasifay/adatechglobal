# Convert fetched .html files into .php pages
# - Backs up existing .php to .php.bak
# - Replaces references to .html with .php in href/src attributes

$base = "d:\Xampp\htdocs\UpConstruction-1.0.0"
$map = @{
    "index.html" = "index.php"
    "about.html" = "about.php"
    "services.html" = "services.php"
    "projects.html" = "projects.php"
    "sample-inner-page.html" = "team.php"
    "project-details.html" = "project-details.php"
    "service-details.html" = "service-details.php"
    "contact.html" = "contact.php"
    "blog.html" = "blog.php"
    "blog-details.html" = "blog-details.php"
}

foreach ($htmlFile in $map.Keys) {
    $src = Join-Path $base $htmlFile
    $dst = Join-Path $base $map[$htmlFile]
    $bak = "$dst.bak"
    if (-not (Test-Path $src)) {
        Write-Host "Source $src not found, skipping"
        continue
    }
    try {
        if (Test-Path $dst) {
            Copy-Item -Path $dst -Destination $bak -Force
            Write-Host "Backed up $dst -> $bak"
        }
    } catch {
        Write-Host "Warning backing up" $dst ":" $_
    }

    $content = Get-Content -Path $src -Raw -ErrorAction Stop
    # Replace .html links to .php (naive replacement) but avoid replacing asset paths (assets/*.html unlikely)
    $content = $content -replace '(href\s*=\s*"|src\s*=\s*"|action\s*=\s*\")([^">]+?)\.html(#[^">]*)?"','$1$2.php$3"'
    # Also handle single-quoted attributes
    $content = $content -replace "(href\s*=\s*'|src\s*=\s*'|action\s*=\s*')([^']+?)\.html(#?[^']*)?'","$1$2.php$3'"

    # Save as utf8 without BOM
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($dst, $content, $utf8NoBom)
    Write-Host "Wrote $dst (from $src)"
}

Write-Host "Conversion complete. Review the updated .php files and test locally."