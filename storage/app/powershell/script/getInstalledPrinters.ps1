param ($hostname, $export)

$ErrorActionPreference = 'Stop'

try {

    . $PSScriptRoot\CredsKSTT.ps1

    $data = Invoke-Command -ComputerName $hostname -Credential $credential -Scriptblock {

        $default = @(
            'PDFCreator'
            'Microsoft XPS Document Writer'
            'Microsoft Print to PDF'
            'OneNote for Windows 10'
            'Fax'
            'Tlaciaren pre ZEP'
        )

        return Get-WmiObject -Class Win32_Printer | ForEach-Object {

            if ($default -notcontains $_.Name) { return $_ }

        }

    }

    if ($data) { $data | Select Name | Export-Csv -NoTypeInformation -encoding utf8 -Path $export }
    else { throw 'No printer found' }

} catch {

    Write-Host $_

    exit 1

}