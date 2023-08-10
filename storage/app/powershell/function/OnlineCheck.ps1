function OnlineCheck($hostname, $sid)
{
    if (Test-Connection -Count 1 -ComputerName $hostname -Quiet) { return $true }
    else {

        if ($sid) { throw '[offline]' }
        else {

            if (Test-Connection -Count 4 -TimeoutSeconds 1 -ComputerName $hostname -Quiet) { return $true }
            else { return $false }

        }

    }
}