param ($sid, $export)

$ErrorActionPreference = 'Stop'

try {

    . $PSScriptRoot\CredsDB.ps1

    $query = 'SELECT SID, Name FROM servers '

    if ($sid) {

        $query = $query + "WHERE SID='$sid'"

    } else {

        $query = $query + 'ORDER BY Name ASC'

    }

    $collection = Invoke-SqlQuery -Query $query

    if ($collection) {

        if ($sid) {

            . $PSScriptRoot\..\function\ServerSpecs.ps1

            $data = New-Object PSObject -property (ServerSpecs -pc $collection -sid 1)

            if (-Not $data.SID) { throw '[winRM]' }

        } else {

            $data = $collection | ForEach-Object -parallel {

                . $using:PSScriptRoot\..\function\ServerSpecs.ps1

                return ServerSpecs -pc $_

            } -ThrottleLimit 100

        }

        $data | Select Name, SID, RegisterVersion, TotalPhysicalMemory, OSArchitecture, Model, LastBootUpTime, InstallDate, Powershell, SN | Export-Csv -NoTypeInformation -encoding utf8 -Path $export

    } else {

        if ($sid) { throw '[not found]' }
        else { throw 'Table [servers] is empty.' }

    }

    Close-SqlConnection

} catch {

    Close-SqlConnection

    Write-Host $_

    exit 1

}