param ($mac)

$ErrorActionPreference = 'Stop'

try {

    $MacByteArray = $mac -split "[:-]" | ForEach-Object { [Byte] "0x$_"}

    [Byte[]] $MagicPacket = (,0xFF * 6) + ($MacByteArray  * 16)

    $UdpClient = New-Object System.Net.Sockets.UdpClient

    $UdpClient.Connect(([System.Net.IPAddress]::Broadcast),7)

    $UdpClient.Send($MagicPacket,$MagicPacket.Length) | Out-Null

    $UdpClient.Close()

} catch {

    Write-Host $_

    exit 1

}