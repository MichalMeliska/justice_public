function ServerSpecs($pc, $sid)
{
    . $PSScriptRoot\OnlineCheck.ps1

    if (-Not (OnlineCheck -hostname $pc.Name -sid $sid)) { return }

    . $PSScriptRoot\Specs.ps1

    if ($sid) {

        . $PSScriptRoot\..\script\CredsAdmin.ps1

        $specs = Invoke-Command -ComputerName $pc.Name -ArgumentList 0,1 -Credential $credential -Scriptblock ${function:Specs}

    } else {

        $specs = Invoke-Command -ComputerName $pc.Name -ArgumentList 0,1 -ErrorAction SilentlyContinue -Scriptblock ${function:Specs}

    }

    if ($specs) {

        return @{
            RegisterVersion = $specs.RegisterVersion
            TotalPhysicalMemory = $specs.TotalPhysicalMemory
            OSArchitecture = $specs.OSArchitecture
            Model = $specs.Model
            SN = $specs.SN
            LastBootUpTime = $specs.LastBootUpTime
            InstallDate = $specs.InstallDate
            Powershell = $specs.Powershell
            SID = $pc.SID                       # because of upsert in savePcDetails
            Name = $pc.Name
        }

    }

}