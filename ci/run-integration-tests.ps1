param (
    [Parameter(Mandatory=$true)]
    [string]$RepoName,
    [Parameter(Mandatory=$true)]
    [hashtable]$Keys
)

if (!$Keys.TestResourceKey) {
    Write-Output "::warning file=$($MyInvocation.ScriptName),line=$($MyInvocation.ScriptLineNumber),title=No Resource Key::No resource key was provided, so integration tests will not run."
    return
} elseif (!(Test-Path $RepoName/tests/51Degrees.csv)) {
    Write-Output "::warning file=$($MyInvocation.ScriptName),line=$($MyInvocation.ScriptLineNumber),title=No CSV File::CSV file wasn't found, so integration tests will not run."
    return
}

$env:RESOURCEKEY = $Keys.TestResourceKey
$env:AcceptChPlatformKey = $Keys.TestResourceKey
$env:AcceptChHardwareKey = $Keys.TestResourceKey
$env:AcceptChBrowserKey = $Keys.TestResourceKey
$env:AcceptChNoneKey = $Keys.TestResourceKey

./php/run-integration-tests.ps1 -RepoName $RepoName

exit $LASTEXITCODE
