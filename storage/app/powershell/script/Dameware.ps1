param ($hostname)

$ErrorActionPreference = 'Stop'

try {

    . $PSScriptRoot\CredsLocal.ps1

    & "C:\Program Files\SolarWinds\DameWare Mini Remote Control x64\DWRCC.exe" -c: -h: -m:$hostname -u:$user -p:$pwd | Out-Null

} catch {

    Write-Host $_

    exit 1

}