# Fetch live HTML pages from www.adatechglobal.com and save into local repo
# Backups: existing target files are copied to *.fetch.bak before overwrite

$base = "d:\Xampp\htdocs\UpConstruction-1.0.0"
$pages = @{
    'https://www.adatechglobal.com/index.html' = 'index.html'
    'https://www.adatechglobal.com/about.html' = 'about.html'
    'https://www.adatechglobal.com/services.html' = 'services.html'
    'https://www.adatechglobal.com/projects.html' = 'projects.html'
    'https://www.adatechglobal.com/sample-inner-page.html' = 'sample-inner-page.html'
    'https://www.adatechglobal.com/project-details.html' = 'project-details.html'
    'https://www.adatechglobal.com/service-details.html' = 'service-details.html'
    'https://www.adatechglobal.com/contact.html' = 'contact.html'
    'https://www.adatechglobal.com/blog.html' = 'blog.html'
    'https://www.adatechglobal.com/blog-details.html' = 'blog-details.html'
}

Write-Host "Fetching ${($pages.Count)} pages to $base"

foreach ($url in $pages.Keys) {
    $targetRel = $pages[$url]
    $target = Join-Path $base $targetRel
    $bak = "$target.fetch.bak"
    try {
        if (Test-Path $target) {
            Copy-Item -Path $target -Destination $bak -Force
            Write-Host "Backed up $target -> $bak"
        }
    } catch {
        Write-Host "Warning: could not backup" $target ":" $_
    }

    Write-Host "Fetching $url ..."
    try {
        $resp = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 30 -ErrorAction Stop
        $html = $resp.Content
        # Save as UTF8 without BOM
        $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
        [System.IO.File]::WriteAllText($target, $html, $utf8NoBom)
        Write-Host "Saved $target (Length: $($html.Length))"
    } catch {
        Write-Host "Failed to fetch $url : $_"
    }
}

Write-Host "Done fetching pages. Review fetched files in $base."
