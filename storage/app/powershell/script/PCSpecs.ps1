param ($sid, $export)

$ErrorActionPreference = 'Stop'

try {

    . $PSScriptRoot\CredsDB.ps1

    $query = 'SELECT SID, Name, sud, IT FROM computers '

    if ($sid) {

        $query = $query + "WHERE SID='$sid'"

    } else {

        $query = $query + 'WHERE specs_at IS NULL OR DATE(specs_at) != CURDATE() OR LoggedUser IS NULL ORDER BY Name ASC'

    }

    $collection = Invoke-SqlQuery -Query $query

    if ($collection) {

        $query = 'SELECT SID, SamAccountName FROM users'
        $result = Invoke-SqlQuery -Query $query
        $users = @{}
        foreach ($row in $result) { $users[$row.SamAccountName] = $row.SID }

        if ($sid) {

            if ($collection.IT) { throw '[admin]' }

            . $PSScriptRoot\..\function\PCSpecs.ps1

            $data = New-Object PSObject -property (PCSpecs -pc $collection -users $users -sid 1)

            if (-Not $data.SID) { throw '[winRM]' }

        } else {

            $data = $collection | ForEach-Object -parallel {

                if (-Not $_.IT) {

                    . $using:PSScriptRoot\..\function\PCSpecs.ps1

                    return PCSpecs -pc $_ -users $using:users

                }

            } -ThrottleLimit 500

        }

        $data | Select Name, SID, RegisterVersion, TotalPhysicalMemory, OSArchitecture, Model, LastBootUpTime, InstallDate, Mac, LoggedUser, sud, Printer, Powershell, IP, IT, SN | Export-Csv -NoTypeInformation -encoding utf8 -Path $export

    } else {

        if ($sid) { throw '[not found]' }

    }

    Close-SqlConnection

} catch {

    Close-SqlConnection

    Write-Host $_

    exit 1

}