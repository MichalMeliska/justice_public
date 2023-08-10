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

            . $PSScriptRoot\..\function\ServerRunning.ps1

            $data = New-Object PSObject -property (ServerRunning -pc $collection -sid 1)

            if (-Not $data.SID) { throw '[winRM]' }

        } else {

            $data = $collection | ForEach-Object -parallel {

                . $using:PSScriptRoot\..\function\ServerRunning.ps1

                return ServerRunning -pc $_

            } -ThrottleLimit 100

        }

        $data | Select Name, SID, RegisterRunning, TaskSchedulerRunning, RegisterLog, offline | Export-Csv -NoTypeInformation -encoding utf8 -Path $export

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