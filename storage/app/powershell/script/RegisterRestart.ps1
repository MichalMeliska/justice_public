param ($podatelna)

$ErrorActionPreference = 'Stop'

try {

    $synccdb = 'SyncCDB'
    $synckonk = 'SyncKonk'

    $register_ks = 'RegisterKS'
    $register_os = 'Register'

    $sync_process = $synccdb, $synckonk
    $reg_process = $register_ks, $register_os

    $sched_job = 'Register_server'
    $sched_job_start = 'register_server_start'

    $username = 'JUSTICE\zaloha.kstt'

    $log_path = "\\$podatelna\c$\Register\LogServer.txt"

    . $PSScriptRoot\CredsAdmin.ps1


    foreach ($process in $reg_process) {

        Invoke-Command -ComputerName $podatelna -Credential $credential -ScriptBlock {

            $proc = Get-Process $using:process -ErrorAction SilentlyContinue -IncludeUserName | Where-Object {$_.username -eq $using:username}

            if ($proc) { $proc | Stop-Process -Force }

        }

    }

    foreach ($process in $sync_process) {

        Invoke-Command -ComputerName $podatelna -Credential $credential -ScriptBlock {

            $proc = Get-Process $using:process -ErrorAction SilentlyContinue

            if ($proc) { $proc | Stop-Process -Force }

        }

    }


    if (@('sposga', 'spkstt') -contains $hostname) { $job = $sched_job_start  }
    else { $job = $sched_job }

    Invoke-Command -ComputerName $podatelna -Credential $credential -ScriptBlock { Start-ScheduledTask -TaskName $using:job }

    Start-Sleep -s 5


    $log_date_time, $msg = Invoke-Command -ComputerName $podatelna -Credential $credential -ScriptBlock {

        return (Get-content -tail 1 $using:log_path) -split ":\s+"

    }

    $last_log_entry = Get-Date -Date $log_date_time -UFormat %s
    $cur_time, $x = (Get-Date -UFormat %s) -split ","
    $diff = [int]$cur_time - [int]$last_log_entry

    if ($diff -gt 10) { throw '[register]' }

} catch {

    Write-Host $_

    exit 1

}