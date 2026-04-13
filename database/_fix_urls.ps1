$viewsDir = "c:\xampp\htdocs\GoPlay\app\views"
$files = Get-ChildItem -Recurse $viewsDir -Filter *.php
$totalFixed = 0
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $original = $content
    # Fix fetch('/api/...' to fetch((window.BASE_URL||'')+'/api/...'
    $content = $content -replace "fetch\('/api/", "fetch((window.BASE_URL||'')+'/api/"
    # Fix fetch(`/api/ to fetch(`${window.BASE_URL||''}/api/
    $content = $content -replace 'fetch\(`/api/', 'fetch(`${window.BASE_URL||''''}/api/'
    if ($content -ne $original) {
        Set-Content $file.FullName -Value $content -Encoding UTF8 -NoNewline
        $count = ([regex]::Matches($original, "fetch\('/api/")).Count + ([regex]::Matches($original, 'fetch\(`/api/')).Count
        Write-Host "$($file.Name): Fixed $count fetch calls"
        $totalFixed += $count
    }
}
Write-Host "`nTotal fixed: $totalFixed fetch calls across all views"
