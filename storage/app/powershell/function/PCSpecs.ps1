function PCSpecs($pc, $users, $sid)
{
    . $PSScriptRoot\OnlineCheck.ps1

    if (-Not (OnlineCheck -hostname $pc.Name -sid $sid)) { return }

    $sud = $pc.Name.substring(0, 2).ToLower()

    $reg_ks = if ($sud -eq 'ks') { 1 } else { 0 }

    . $PSScriptRoot\Specs.ps1

    $specs = Invoke-Command -ComputerName $pc.Name -ArgumentList $reg_ks,0,$users -ErrorAction SilentlyContinue -Scriptblock ${function:Specs}

    if ($specs) {

        return @{
            RegisterVersion = $specs.RegisterVersion
            TotalPhysicalMemory = $specs.TotalPhysicalMemory
            OSArchitecture = $specs.OSArchitecture
            Model = $specs.Model
            LastBootUpTime = $specs.LastBootUpTime
            InstallDate = $specs.InstallDate
            Mac = $specs.Mac
            IP = $specs.IP
            SN = $specs.SN
            LoggedUser = $specs.LoggedUser
            Powershell = $specs.Powershell
            Printer = $specs.Printer
            SID = $pc.SID                       # because of upsert in savePcDetails
            Name = $pc.Name
            sud = $pc.sud
            IT = $pc.IT
        }

    }
}