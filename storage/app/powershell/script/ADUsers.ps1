param ($sid, $export)

$ErrorActionPreference = 'Stop'

try {

    $date_format = 'yyyy-MM-dd HH:mm:ss'

    if ($sid) {

        $result = Get-ADUser -Identity $sid -prop ipPhone, EmailAddress, Created, Description, LockedOut, msExchWhenMailboxCreated, PasswordLastSet, CanonicalName, "msDS-UserPasswordExpiryTimeComputed" | Select Name, SamAccountName, EmailAddress, SID, Enabled, ipPhone, Description, LockedOut, CanonicalName, @{Name='Created'; Expression={ $_.Created.ToString($date_format) }}, @{Name='msExchWhenMailboxCreated'; Expression={ $_.msExchWhenMailboxCreated.ToString($date_format) }}, @{Name='PasswordLastSet'; Expression={ $_.PasswordLastSet.ToString($date_format) }}, @{Name='PasswordExpires';Expression={[datetime]::FromFileTime($_."msDS-UserPasswordExpiryTimeComputed").ToString($date_format)}}

    } else {

        $OUS = @(
            "OU=_informatici,OU=Users,OU=KS-TT,DC=justice,DC=sk",
            "OU=_Ostatni,OU=Users,OU=KS-TT,DC=justice,DC=sk",
            "OU=Users,OU=OS-TT,OU=KS-TT,DC=justice,DC=sk",
            "OU=Users,OU=OS-PN,OU=KS-TT,DC=justice,DC=sk",
            "OU=Users,OU=OS-DS,OU=KS-TT,DC=justice,DC=sk",
            "OU=Users,OU=OS-GA,OU=KS-TT,DC=justice,DC=sk",
            "OU=Users,OU=OS-SE,OU=KS-TT,DC=justice,DC=sk",
            "OU=Users,OU=OS-SI,OU=KS-TT,DC=justice,DC=sk"
        )

        $result = foreach($OU in $OUS) {
            Get-ADUser -Filter * -SearchBase $OU -SearchScope 1 -prop ipPhone, EmailAddress, Created, Description, LockedOut, msExchWhenMailboxCreated, PasswordLastSet, CanonicalName, "msDS-UserPasswordExpiryTimeComputed" | Select Name, SamAccountName, EmailAddress, SID, Enabled, ipPhone, Description, LockedOut, CanonicalName, @{Name='Created'; Expression={ $_.Created.ToString($date_format) }}, @{Name='msExchWhenMailboxCreated'; Expression={ $_.msExchWhenMailboxCreated.ToString($date_format) }}, @{Name='PasswordLastSet'; Expression={ $_.PasswordLastSet.ToString($date_format) }}, @{Name='PasswordExpires';Expression={[datetime]::FromFileTime($_."msDS-UserPasswordExpiryTimeComputed").ToString($date_format)}}
        }

    }

    if ($result) { $result | Export-Csv -NoTypeInformation -encoding utf8 -Path $export }
    else {

        if ($sid) { throw 'Unexistent user' }
        else { throw 'ADUsers - empty list' }

    }

} catch {

    Write-Host $_

    exit 1

}