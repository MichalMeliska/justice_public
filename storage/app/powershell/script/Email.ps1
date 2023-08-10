param ($address)

$ErrorActionPreference = 'Stop'

try {

    $Outlook = New-Object -ComObject Outlook.Application
    $Mail = $Outlook.CreateItem(0)
    $Mail.To = $address
    $Mail.Display()

} catch {

    Write-Host $_

    exit 1

}