param ($sid, $hostname, $printerName)

$ErrorActionPreference = 'Stop'

try {

    . $PSScriptRoot\CredsKSTT.ps1

    Invoke-Command -ComputerName $hostname -Credential $credential -ArgumentList $sid, $printerName -Scriptblock {

        param($sid, $printerName)

        $spool = Get-ItemProperty -Path "REGISTRY::HKEY_USERS\$sid\Software\Microsoft\Windows NT\CurrentVersion\Devices" -name $printerName

        Set-ItemProperty -Path "REGISTRY::HKEY_USERS\$sid\Software\Microsoft\Windows NT\CurrentVersion\Windows" -name Device -Value "$printerName,$spool"

        Set-ItemProperty -Path "REGISTRY::HKEY_USERS\$sid\Software\Microsoft\Windows NT\CurrentVersion\Windows" -name LegacyDefaultPrinterMode -Value 1

    }

} catch {

    Write-Host $_

    exit 1

}