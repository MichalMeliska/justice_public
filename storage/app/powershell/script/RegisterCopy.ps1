param ($hostname)

$ErrorActionPreference = 'Stop'

try {

    $sud = $hostname.substring(0, 2).ToLower()

    $exe = @('Register')

    if ($sud -eq 'ks') { $exe += 'RegisterKS' }

    . $PSScriptRoot\CredsAdmin.ps1

    Invoke-Command -ComputerName $hostname -Credential $credential -ArgumentList $exe -Scriptblock {

        param ($exe)

        Stop-Process -ProcessName $exe -ErrorAction SilentlyContinue

    }

    $Session = New-PSSession -ComputerName $hostname -Credential $credential

    foreach ($e in $exe) {

        Copy-Item -Path "\\spkstt\c$\register\$e.exe" -Destination "C:\Register\" -ToSession $Session

    }

} catch {

    Write-Host $_

    exit 1

}