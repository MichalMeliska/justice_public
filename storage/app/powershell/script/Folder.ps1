param ($hostname, $admin)

$ErrorActionPreference = 'Stop'

try {

    if ($admin) {

        . $PSScriptRoot\CredsAdmin.ps1

        New-PSDrive -Name P -PSProvider FileSystem -Root \\$hostname\c$ -Credential $credential

        Invoke-Item P:

    } else { Invoke-Item \\$hostname\c$ }

} catch {

    Write-Host $_

    exit 1

}