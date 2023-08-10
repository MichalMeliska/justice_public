param ($sid, $export)

$ErrorActionPreference = 'Stop'

try {

    $date_format = 'yyyy-MM-dd HH:mm:ss'

    if ($sid) {

        $result = Get-ADComputer -Identity $sid -prop Created, OperatingSystem, CanonicalName | Select Name, OperatingSystem, CanonicalName, SID, @{Name='Created'; Expression={ $_.Created.ToString($date_format) }}, @{Name='IT'; Expression={ $(if ($_.CanonicalName.ToLower().Contains('informatika')) { 1 } else { 0 }) }}

    } else {

        $OUS = @(
            'OU=informatika,OU=PC,OU=KS-TT,DC=justice,DC=sk',
            'OU=ostatne,OU=PC,OU=KS-TT,DC=justice,DC=sk',
            'OU=vyberko,OU=PC,OU=KS-TT,DC=justice,DC=sk',
            'OU=ostatne,OU=PC,OU=OS-TT,OU=KS-TT,DC=justice,DC=sk',
            'OU=ORTT,OU=PC,OU=OS-TT,OU=KS-TT,DC=justice,DC=sk',
            'OU=ostatne,OU=PC,OU=OS-PN,OU=KS-TT,DC=justice,DC=sk',
            'OU=ostatne,OU=PC,OU=OS-DS,OU=KS-TT,DC=justice,DC=sk',
            'OU=zvjs,OU=PC,OU=OS-DS,OU=KS-TT,DC=justice,DC=sk',
            'OU=ostatne,OU=PC,OU=OS-GA,OU=KS-TT,DC=justice,DC=sk',
            'OU=ostatne,OU=PC,OU=OS-SE,OU=KS-TT,DC=justice,DC=sk',
            'OU=ostatne,OU=PC,OU=OS-SI,OU=KS-TT,DC=justice,DC=sk'
        )

        $result = foreach($OU in $OUS) {
            Get-ADComputer -Filter * -SearchBase $OU -SearchScope 1 -prop Created, OperatingSystem, CanonicalName | Select Name, OperatingSystem, CanonicalName, SID, @{Name='Created'; Expression={ $_.Created.ToString($date_format) }}, @{Name='IT'; Expression={ $(if ($_.CanonicalName.ToLower().Contains('informatika')) { 1 } else { 0 }) }}
        }

    }

    if ($result) { $result | Export-Csv -NoTypeInformation -encoding utf8 -Path $export }
    else {

        if ($sid) { throw 'Unexistent computer' }
        else { throw 'ADComputers - empty list' }

    }

} catch {

    Write-Host $_

    exit 1

}