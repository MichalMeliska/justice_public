function Specs($reg_ks, $server, $users)
{
    $date_format = 'yyyy-MM-dd HH:mm:ss'

    ######### REGISTER #########
    $exe = if ($reg_ks) { 'RegisterKS.exe' } else { 'Register.exe' }

    $reg_version = (Get-ItemProperty -ErrorAction SilentlyContinue -Path "C:\Register\$exe").VersionInfo.FileVersion
    ######### REGISTER #########


    ######### OS #########
    $os = Get-WmiObject -Class Win32_OperatingSystem | Select-Object LastBootUpTime, InstallDate, OSArchitecture

    $boot = [Management.ManagementDateTimeConverter]::ToDateTime($os.LastBootUpTime)
    $boot = Get-Date $boot -Format $date_format

    $install = [Management.ManagementDateTimeConverter]::ToDateTime($os.InstallDate)
    $install = Get-Date $install -Format $date_format

    $bit = $os.OSArchitecture -replace '[^0-9]'
    ######### OS #########


    ######### PC #########
    $comp = Get-WmiObject -Class Win32_ComputerSystem | Select-Object Model, TotalPhysicalMemory, Username

    $model = $comp.Model

    $ram = [math]::Ceiling($comp.TotalPhysicalMemory / (1024 * 1024 * 1024))

    $logged_user = if ($comp.Username) { $comp.Username.Split('\')[1] } else { '' }
    ######### PC #########

    ######### BIOS #########
    if ($model -eq 'Virtual Machine') { $sn = '' }
    else { $sn = (Get-WmiObject -Class Win32_bios).Serialnumber }
    ######### BIOS #########

    ######### POWERSHELL #########
    $powershell = $PSVersionTable.PSVersion.Major
    ######### POWERSHELL #########


    if (-Not ($server)) {

        $adapter = Get-WmiObject Win32_NetworkAdapterConfiguration -Filter 'DNSDomain = "justice.sk"'

        if (-Not ($adapter)) { $adapter = Get-WmiObject Win32_NetworkAdapterConfiguration -Filter 'DHCPEnabled = "True"' }

        ######### IP #########
        $ip = $adapter.IPAddress[0]
        ######### IP #########


        ######### MAC #########
        $mac = $adapter.MACAddress

        $mac = ([string]$mac).Trim()
        ######### MAC #########


        ######### PRINTER #########
        $printer = ''

        if ($logged_user) {

            $logged_sid = $users[$logged_user]

            if ($logged_sid) {

                $printer = (Get-ItemProperty -Path "REGISTRY::HKEY_USERS\$logged_sid\Software\Microsoft\Windows NT\CurrentVersion\Windows").Device

            }

        }

        <#if (-Not ($sid) -And $pc.OperatingSystem -match 10) {

            $default_printers = @(
                'PDFCreator',
                'Microsoft XPS Document Writer',
                'Microsoft Print to PDF',
                'OneNote for Windows 10',
                'Fax',
                'Tlaciaren pre ZEP'
            )

            foreach ($p in get-printer) {

                if ($default_printers -notcontains $p.Name) {

                    Set-Printer $p.Name -PermissionSDDL 'G:SYD:(A;;SWRC;;;WD)(A;;SWRC;;;AC)(A;CIIO;RC;;;AC)(A;OIIO;RPWPSDRCWDWO;;;AC)(A;;SWRC;;;S-1-15-3-1024-4044835139-2658482041-3127973164-329287231-3865880861-1938685643-461067658-1087000422)(A;CIIO;RC;;;S-1-15-3-1024-4044835139-2658482041-3127973164-329287231-3865880861-1938685643-461067658-1087000422)(A;OIIO;RPWPSDRCWDWO;;;S-1-15-3-1024-4044835139-2658482041-3127973164-329287231-3865880861-1938685643-461067658-1087000422)(A;CIIO;RC;;;CO)(A;OIIO;RPWPSDRCWDWO;;;CO)(A;OIIO;RPWPSDRCWDWO;;;S-1-5-21-1772437827-792146050-1153772777-66068)(A;;LCSWSDRCWDWO;;;S-1-5-21-1772437827-792146050-1153772777-66068)(A;OIIO;RPWPSDRCWDWO;;;BA)(A;;LCSWSDRCWDWO;;;BA)(A;;LCSWSDRCWDWO;;;BU)(A;OIIO;RPWPSDRCWDWO;;;BU)'

                }

            }

        }#>
        ######### PRINTER #########

    } else {

        $mac = ''
        $ip = ''
        $printer = ''

    }

    return @{
        RegisterVersion = $reg_version
        TotalPhysicalMemory = $ram
        OSArchitecture = $bit
        Model = $model
        LastBootUpTime = $boot
        InstallDate = $install
        Mac = $mac
        IP = $ip
        LoggedUser = $logged_user
        Powershell = $powershell
        Printer = $printer
        SN = $sn
    }
}