param ($sid, $export)

$ErrorActionPreference = 'Stop'

try {

    $date_format = 'yyyy-MM-dd HH:mm:ss'

    if ($sid) {

        $result = Get-ADComputer -Identity $sid -prop Created, OperatingSystem | Select Name, OperatingSystem, SID, @{Name='Created'; Expression={ $_.Created.ToString($date_format) }}

    } else {

        $OUS = @(
            "OU=KS-TT,DC=justice,DC=sk",
            "OU=KSTT,OU=Domain Controllers,DC=justice,DC=sk"
        )

        $result = foreach($OU in $OUS) {
            Get-ADComputer -Filter * -SearchBase $OU -SearchScope 2 -prop Created, OperatingSystem | where { ($_.DistinguishedName -notmatch 'OU=ostatne,|OU=notebooky,|OU=vyberko,|OU=internetPC,|OU=informatika,|OU=Infokiosk,|OU=ORTT,|OU=zvjs,')} | Select Name, OperatingSystem, SID, @{Name='Created'; Expression={ $_.Created.ToString($date_format) }}
        }

    }

    if ($result) { $result | Export-Csv -NoTypeInformation -encoding utf8 -Path $export }
    else {

        if ($sid) { throw 'Unexistent server' }
        else { throw 'ADServers - empty list' }

    }

} catch {

    Write-Host $_

    exit 1

}