param (
    [Parameter(Mandatory=$true)]
    [string]$RepoName,
    [Parameter(Mandatory=$true)]
    [string]$DeviceDetection
)

if ($env:GITHUB_JOB -eq "PreBuild") {
    Write-Output "Skipping assets fetching"
    exit 0
}

$assets = New-Item -ItemType Directory -Path assets -Force
$file = "51Degrees.csv"

if (!(Test-Path $assets/$file)) {
    Write-Output "Downloading $file"
    Invoke-WebRequest -Uri "https://storage.googleapis.com/51degrees-assets/$DeviceDetection/51Degrees-Tac.zip" -OutFile 51Degrees-Tac.zip
    Expand-Archive -Path 51Degrees-Tac.zip
    Get-Content -TotalCount 1 51Degrees-Tac/51Degrees-Tac-All.csv | Out-File $assets/$file # We only need a header
    Remove-Item -Path 51Degrees-Tac.zip, 51Degrees-Tac/51Degrees-Tac-All.csv
} else {
    Write-Output "'$file' exists, skipping download"
}

New-Item -ItemType SymbolicLink -Force -Target "$assets/$file" -Path "$RepoName/tests/$file"
