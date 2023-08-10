param ($export)

$ErrorActionPreference = 'Stop'

try {

    . $PSScriptRoot\CredsDB.ps1

    $collection = Invoke-SqlQuery -Query 'SELECT SID, Name, sud, IT FROM computers ORDER BY Name ASC'

    if ($collection) {

        $data = $collection | ForEach-Object -parallel {

            . $using:PSScriptRoot\..\function\OnlineCheck.ps1

            if (OnlineCheck -hostname $_.Name) {

                return @{
                    SID = $_.SID       
                    sud = $_.sud
                    Name = $_.Name
                    IT = $_.IT
                }

            }

        } -ThrottleLimit 500

        $data | Export-Csv -NoTypeInformation -encoding utf8 -Path $export

    } else { throw 'Table [computers] is empty.' }

    Close-SqlConnection

} catch {

    Close-SqlConnection

    Write-Host $_

    exit 1

}