$files = @('about','services','service-details','sample-inner-page','projects','project-details','contact','blog','blog-details')
$cwd = (Get-Location).Path
foreach ($name in $files) {
  $bak = Join-Path $cwd "$name.html.bak"
  $dest = Join-Path $cwd "$name.html"
  if (Test-Path $dest) {
    Copy-Item $dest "$dest.redirect.bak" -Force
  }
  if (Test-Path $bak) {
    Copy-Item $bak $dest -Force
    Write-Host "Restored $name.html from backup"
  } else {
    $content = @"
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>$name (placeholder)</title>
<link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
<main class="container">
  <h1>$name (placeholder)</h1>
  <p>Restore placeholder. Paste missing content here. Dynamic version at $name.php</p>
</main>
</body>
</html>
"@
    $content | Out-File -FilePath $dest -Encoding utf8
    Write-Host "Created placeholder $name.html"
  }
}
