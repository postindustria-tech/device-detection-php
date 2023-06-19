param (
    [Parameter(Mandatory=$true)]
    [string]$RepoName,
    [Parameter(Mandatory=$true)]
    [hashtable]$Keys
)

$env:RESOURCEKEY = $Keys.TestResourceKey
$env:AcceptChPlatformKey = $Keys.TestResourceKey
$env:AcceptChHardwareKey = $Keys.TestResourceKey
$env:AcceptChBrowserKey = $Keys.TestResourceKey
$env:AcceptChNoneKey = $Keys.TestResourceKey

./php/run-integration-tests.ps1 -RepoName $RepoName

exit $LASTEXITCODE
