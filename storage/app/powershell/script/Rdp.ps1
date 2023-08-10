param ($hostname, $admin)

$ErrorActionPreference = 'Stop'

try {

    if ($admin) { . $PSScriptRoot\CredsAdmin.ps1 }
    else { . $PSScriptRoot\CredsKSTT.ps1 }

    cmdkey /generic:TERMSRV/$hostname /user:$user /pass:$pwd | Out-Null

    mstsc /v:$hostname

} catch {

    Write-Host $_

    exit 1

}