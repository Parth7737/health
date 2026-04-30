###############################################################################
# transform-page-headers.ps1
# Converts old <div class="page-title"> pattern to new @section layout sections
# Run from the repo root: .\scripts\transform-page-headers.ps1
###############################################################################

$basePath = "C:\Users\WebDev2WS2\ParaCare-2.0\resources\views\hospital"

function Get-PageIcon {
    param([string]$title)
    $t = $title.ToLower()
    if ($t -match '\bipd\b')                     { return 'U+1F3E5' }  # 🏥
    if ($t -match '\bopd\b|out.?patient')         { return 'U+1FA7A' }  # 🩺
    if ($t -match 'queue|worklist')               { return 'U+1FA7A' }  # 🩺
    if ($t -match '\bbed\b')                      { return 'U+1F6CF' }  # 🛏
    if ($t -match 'pharmacy|medicine|frequency')  { return 'U+1F48A' }  # 💊
    if ($t -match 'radiology')                    { return 'U+1F52C' }  # 🔬
    if ($t -match 'pathology')                    { return 'U+1F9EA' }  # 🧪
    if ($t -match 'staff|hr|human resource')      { return 'U+1F465' }  # 👥
    if ($t -match '\bdoctor\b|physician|specialist') { return 'U+1FA7A' } # 🩺
    if ($t -match 'charge|billing|fee')           { return 'U+1F4B0' }  # 💰
    if ($t -match '\btpa\b|insurance')            { return 'U+1F4C4' }  # 📄
    if ($t -match 'role|permission')              { return 'U+1F511' }  # 🔑
    if ($t -match 'allergy.reaction')             { return 'U+26A0'  }  # ⚠
    if ($t -match 'allergy')                      { return 'U+26A0'  }  # ⚠
    if ($t -match 'appointment')                  { return 'U+1F4CB' }  # 📋
    if ($t -match 'visitor')                      { return 'U+1F465' }  # 👥
    if ($t -match 'department|unit\b')            { return 'U+1F3E2' }  # 🏢
    if ($t -match 'designation')                  { return 'U+1F3F7' }  # 🏷
    if ($t -match 'purchase')                     { return 'U+1F6D2' }  # 🛒
    if ($t -match '\bstock\b')                    { return 'U+1F4E6' }  # 📦
    if ($t -match 'building|floor|ward|room')     { return 'U+1F3E2' }  # 🏢
    if ($t -match 'expiry')                       { return 'U+23F0' }   # ⏰
    if ($t -match 'supplier')                     { return 'U+1F3ED' }  # 🏭
    if ($t -match 'leave.type|leave')             { return 'U+1F4C5' }  # 📅
    if ($t -match 'profile|user')                 { return 'U+1F464' }  # 👤
    if ($t -match 'disease')                      { return 'U+1F3E5' }  # 🏥
    if ($t -match 'symptom')                      { return 'U+1FA7A' }  # 🩺
    if ($t -match 'dietary|diet')                 { return 'U+1F957' }  # 🥗
    if ($t -match 'religion')                     { return 'U+1F4CB' }  # 📋
    if ($t -match 'habit')                        { return 'U+1F4CB' }  # 📋
    if ($t -match 'category')                     { return 'U+1F4C2' }  # 📂
    if ($t -match 'general|hospital.data')        { return 'U+2699'  }  # ⚙
    if ($t -match 'age.group')                    { return 'U+1F4CA' }  # 📊
    if ($t -match 'complain')                     { return 'U+1F4E2' }  # 📢
    if ($t -match 'purpose')                      { return 'U+1F4CB' }  # 📋
    if ($t -match 'priority')                     { return 'U+26A1'  }  # ⚡
    if ($t -match 'patient.category|patient')     { return 'U+1F464' }  # 👤
    return 'U+1F4CB'  # 📋 default
}

# Convert unicode codepoint string to actual emoji char
function ConvertTo-Emoji {
    param([string]$code)
    $cp = [Convert]::ToInt32($code.Replace('U+', ''), 16)
    return [System.Char]::ConvertFromUtf32($cp)
}

$files = Get-ChildItem -Path $basePath -Recurse -Filter "*.blade.php" |
    Where-Object { (Get-Content $_.FullName -Raw) -match 'class="page-title' }

$processed = 0
$failed    = [System.Collections.ArrayList]::new()

foreach ($file in $files) {
    try {
        $rawContent = Get-Content $file.FullName -Raw -Encoding UTF8
        $lines      = $rawContent -split "`r`n|`n"

        # ── Extract @section title ─────────────────────────────────────────
        $m = [regex]::Match($rawContent, "@section\('title'\s*,\s*'([^']+)'\)")
        if (-not $m.Success) {
            [void]$failed.Add("$($file.FullName) >> no @section title found"); continue
        }
        $pageTitle = $m.Groups[1].Value
        $iconCode  = Get-PageIcon $pageTitle
        $icon      = ConvertTo-Emoji $iconCode

        # ── State-machine parse ────────────────────────────────────────────
        $state              = 'pre-content'
        $contentLine        = -1
        $depth              = 0
        $containerFluidLine = -1
        $pageTitleLine      = -1
        $pageTitlePreDepth  = -1
        $h3Text             = ''
        $foundH3            = $false
        $foundActionsDiv    = $false   # guard against 2nd col triggering again
        $inActionsDiv       = $false
        $actionsPreDepth    = -1
        $actionsContent     = [System.Collections.ArrayList]::new()
        $blockStartLine     = -1
        $blockEndLine       = -1
        $parseDone          = $false

        for ($i = 0; $i -lt $lines.Count; $i++) {
            $line       = $lines[$i]
            $lineOpens  = ([regex]::Matches($line, '<div\b')).Count
            $lineCloses = ([regex]::Matches($line, '</div>')).Count

            # ── pre-content: just find @section('content') ──────────────
            if ($state -eq 'pre-content') {
                if ($line -match "@section\('content'\)") {
                    $state = 'scanning'; $contentLine = $i
                }
                continue
            }

            # ── scanning ────────────────────────────────────────────────
            $preDepth = $depth

            if ($pageTitleLine -eq -1) {
                # Still looking for page-title div
                if ($preDepth -eq 0 -and $line -match '<div[^>]*class="container-fluid') {
                    $containerFluidLine = $i
                }
                if ($line -match '<div[^>]*class="[^"]*page-title') {
                    $pageTitleLine     = $i
                    $pageTitlePreDepth = $preDepth
                    # blockStartLine determined after we know Type A vs B
                }
                $depth += $lineOpens - $lineCloses
                continue
            }

            # ── Inside page-title ────────────────────────────────────────
            if (-not $inActionsDiv) {
                # Search for h3 text
                if (-not $foundH3 -and $line -match '<h3[^>]*>\s*(.*?)\s*</h3>') {
                    $h3Text = ($Matches[1] -replace '<[^>]+>', '').Trim()
                    $foundH3 = $true
                }

                # Once h3 found, next col-div = actions column
                if ($foundH3 -and -not $foundActionsDiv -and
                    $line -match '<div[^>]*class="[^"]*\bcol-') {
                    $inActionsDiv    = $true
                    $foundActionsDiv = $true
                    $actionsPreDepth = $preDepth
                    # Do NOT add the opening <div class="col-..."> to actionsContent
                    $depth += $lineOpens - $lineCloses
                    continue
                }

                # Normal line inside page-title (not in actions col)
                $depth += $lineOpens - $lineCloses
            }
            else {
                # ── Inside the actions column div ────────────────────────
                $depth += $lineOpens - $lineCloses

                if ($depth -le $actionsPreDepth) {
                    # Actions col div just closed — this line is its </div>
                    $inActionsDiv = $false
                    # Don't add this closing </div> to actionsContent
                } else {
                    [void]$actionsContent.Add($line)
                }
            }

            # ── Check if page-title block ended ──────────────────────────
            if ($i -gt $pageTitleLine -and $depth -le $pageTitlePreDepth) {
                # Look ahead past blank lines for the next non-empty line
                $j = $i + 1
                while ($j -lt $lines.Count -and $lines[$j] -match '^\s*$') { $j++ }

                if ($containerFluidLine -ne -1 -and
                    $j -lt $lines.Count -and
                    $lines[$j] -match '^\s*</div>\s*$') {
                    # Type A: standalone container-fluid closes right after page-title
                    $blockStartLine = $containerFluidLine
                    $blockEndLine   = $j
                } else {
                    # Type B: page-title is inside a larger container (only remove page-title div)
                    $blockStartLine = $pageTitleLine
                    $blockEndLine   = $i
                }

                $parseDone = $true
                break
            }
        } # end foreach line

        if (-not $parseDone -or $blockStartLine -eq -1 -or $blockEndLine -eq -1) {
            [void]$failed.Add("$($file.FullName) >> parse failed (done=$parseDone start=$blockStartLine end=$blockEndLine)")
            continue
        }

        # ── Clean up actionsContent ────────────────────────────────────────
        $actArr = @($actionsContent)

        # Trim leading/trailing blank lines
        $s = 0; $e = $actArr.Count - 1
        while ($s -le $e -and $actArr[$s] -match '^\s*$') { $s++ }
        while ($e -ge $s -and $actArr[$e] -match '^\s*$') { $e-- }
        $actArr = if ($s -le $e) { $actArr[$s..$e] } else { @() }

        # Dedent to minimum indent
        if ($actArr.Count -gt 0) {
            $nonEmpty  = $actArr | Where-Object { $_ -match '\S' }
            $minIndent = if ($nonEmpty) {
                ($nonEmpty | ForEach-Object {
                    if ($_ -match '^(\s+)') { $Matches[1].Length } else { 0 }
                } | Measure-Object -Minimum).Minimum
            } else { 0 }

            if ($minIndent -gt 0) {
                $actArr = $actArr | ForEach-Object {
                    if ($_.Length -ge $minIndent) { $_.Substring($minIndent) }
                    else                          { $_.TrimStart() }
                }
            }
        }

        # Determine if actions are real buttons (not old-style breadcrumbs)
        $actText        = $actArr -join "`n"
        $hasBreadcrumb  = $actText -match '<ol[^>]*breadcrumb'
        $hasRealActions = ($actText -match '\bbtn\b' -or $actText -match '<button\b') -and -not $hasBreadcrumb

        # ── Build subtitle ─────────────────────────────────────────────────
        $displayTitle = if ($h3Text) { $h3Text } else { $pageTitle }
        $subtitle     = "Manage $displayTitle"

        # ── Build new @section lines block ─────────────────────────────────
        $newSections = [System.Collections.ArrayList]::new()
        [void]$newSections.Add("@section('page_header_icon', '$icon')")
        [void]$newSections.Add("@section('page_subtitle', '$subtitle')")
        if ($hasRealActions -and $actArr.Count -gt 0) {
            [void]$newSections.Add("@section('page_header_actions')")
            foreach ($al in $actArr) { [void]$newSections.Add($al) }
            [void]$newSections.Add("@endsection")
        }

        # ── Rebuild file lines ─────────────────────────────────────────────
        $outputLines = [System.Collections.ArrayList]::new()
        for ($i = 0; $i -lt $lines.Count; $i++) {
            if ($i -eq $contentLine) {
                foreach ($ns in $newSections) { [void]$outputLines.Add($ns) }
                [void]$outputLines.Add($lines[$i])
            } elseif ($i -ge $blockStartLine -and $i -le $blockEndLine) {
                # skip the old page-title block (and wrapper container-fluid if Type A)
            } else {
                [void]$outputLines.Add($lines[$i])
            }
        }

        # Write back with UTF-8 (no BOM)
        $enc = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($file.FullName, ($outputLines -join "`n"), $enc)
        $processed++

    } catch {
        [void]$failed.Add("$($file.FullName) >> EXCEPTION: $_")
    }
}

Write-Host ""
Write-Host "=========================================="
Write-Host "  Processed : $processed"
Write-Host "  Failed    : $($failed.Count)"
Write-Host "=========================================="
if ($failed.Count -gt 0) {
    Write-Host ""
    Write-Host "Failed files:"
    foreach ($f in $failed) { Write-Host "  - $f" }
}
