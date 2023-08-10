Import-Module SimplySql

$db_server = '127.0.0.1'
$db_select = 'justice'
$db_user = ''

$db_cred = New-Object System.Management.Automation.PSCredential($db_user, (new-object System.Security.SecureString))

Open-MySqlConnection -Server $db_server -Database $db_select -Credential $db_cred