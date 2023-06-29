param (
    [Parameter(Mandatory=$true)]
    [string]$RepoName,
    [Parameter(Mandatory=$true)]
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
    Write-Output "Downloading $file"
    ./steps/fetch-csv-assets.ps1 -RepoName $RepoName -LicenseKey $DeviceDetection -Url $CsvUrl
    Get-Content -TotalCount 1 $RepoName/51Degrees-TacV3.4.trie/51Degrees-Tac-All.csv | Out-File $assets/$file # We only need a header
    Remove-Item -Path $RepoName/51Degrees-TacV3.4.trie.zip, $RepoName/51Degrees-TacV3.4.trie/51Degrees-Tac-All.csv
} else {
    Write-Output "'$file' exists, skipping download"
}

New-Item -ItemType SymbolicLink -Force -Target "$assets/$file" -Path "$RepoName/tests/$file"
