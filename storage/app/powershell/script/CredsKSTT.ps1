$user = ''
$pwd = ''

$pwd_secure = ConvertTo-SecureString -String $pwd -AsPlainText -Force

$credential = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $user, $pwd_secure