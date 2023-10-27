param (
    [Parameter(Mandatory=$true)]
    [string]$RepoName,
    [string]$DeviceDetection,
    [string]$CsvUrl
)

if ($env:GITHUB_JOB -eq "PreBuild") {
    Write-Output "Skipping assets fetching"
    exit 0
}

$assets = New-Item -ItemType Directory -Path assets -Force
$file = "51Degrees.csv"

if (!(Test-Path $assets/$file)) {
    if (!$DeviceDetection) {
        Write-Output "::warning file=$($MyInvocation.ScriptName),line=$($MyInvocation.ScriptLineNumber),title=No License Key::A device detection license was not provided, so '$file' will not be downloaded."
        return
    }
    Write-Output "Downloading $file"
    ./steps/fetch-csv-assets.ps1 -RepoName $RepoName -LicenseKey $DeviceDetection -Url $CsvUrl
    Get-Content -TotalCount 1 $RepoName/51Degrees-Tac/51Degrees-Tac-All.csv | Out-File $assets/$file # We only need a header
    Remove-Item -Path $RepoName/51Degrees-Tac.zip, $RepoName/51Degrees-Tac/51Degrees-Tac-All.csv
} else {
    Write-Output "'$file' exists, skipping download"
}

New-Item -ItemType SymbolicLink -Force -Target "$assets/$file" -Path "$RepoName/tests/$file"
