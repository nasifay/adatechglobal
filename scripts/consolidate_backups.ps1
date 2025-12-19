$base = "d:\Xampp\htdocs\UpConstruction-1.0.0"
$files = Get-ChildItem -Path $base -Include *.bak -File -Recurse -ErrorAction SilentlyContinue
if (-not $files) { Write-Host "No .bak files found."; exit 0 }
foreach ($f in $files) {
    $rel = $f.FullName.Substring($base.Length+1) -replace '[\\/:]','_'
    $dest = Join-Path $base ("backups\" + $rel)
    $dirdest = Split-Path $dest
    if (-not (Test-Path $dirdest)) { New-Item -ItemType Directory -Path $dirdest -Force | Out-Null }
    Move-Item -Path $f.FullName -Destination $dest -Force
    Write-Host "Moved $($f.FullName) -> $dest"
}
Write-Host "Consolidation complete."
