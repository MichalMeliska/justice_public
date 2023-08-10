function ServerRunning($pc, $sid)
{
    . $PSScriptRoot\OnlineCheck.ps1

    if (-Not (OnlineCheck -hostname $pc.Name -sid $sid)) {

        if (`
            (`
                $pc.Name.ToLower() -match '^sp' -And `
                $pc.Name.ToLower() -notmatch 'stary$' -And `
                $pc.Name.ToLower() -ne 'spossi' -And `
                $pc.Name.ToLower() -ne 'spospn' `
            ) -Or `
            $pc.Name.ToLower() -match '^shyp' -Or `
            $pc.Name.ToLower() -eq 'stt0dbmsqp01sm' `
        ) {

            return @{
                offline = 1
                Name = $pc.Name
            }

        } else { return }

    }

    if (`
        -Not ( `
            $pc.Name.ToLower() -match '^sp' -And `
            $pc.Name.ToLower() -notmatch 'stary$' -And `
            $pc.Name.ToLower() -ne 'spossi' -And `
            $pc.Name.ToLower() -ne 'spospn' `
        )
    ) {

        return @{
            SID = $pc.SID
            Name = $pc.Name
        }

    }

    . $PSScriptRoot\..\script\CredsAdmin.ps1

    return Invoke-Command -ComputerName $pc.Name -Credential $credential -ArgumentList $pc -Scriptblock {

        param($pc)


        ######### REG_RUNNING #########
        if (Get-Process -IncludeUserName | Where-Object { $_.username -eq 'JUSTICE\zaloha.kstt' -and ($_.processname -eq 'Register' -or $_.processname -eq 'RegisterKS') }) {

            $reg_running = 1

        } else { $reg_running = 0 }
        ######### REG_RUNNING #########


        ######### JOB_RUNNING #########
        if ($pc.Name.ToLower() -match 'kstt|osga') {

            $job_name = 'register_server_start'

        } else {

            $job_name = 'Register_server'

        }

        if (Get-ScheduledTask | Where-Object { $_.taskname -eq $job_name -and $_.state -eq 'Running' }) {

            $sched_job = 1

        } else {

            $sched_job = 0

        }
        ######### JOB_RUNNING #########


        ######### LOG #########
        $log_date_time, $msg = (Get-Content -tail 1 'C:\Register\LogServer.txt') -split ':\s+'
        $last_log_entry = Get-Date -Date $log_date_time -UFormat %s
        $cur_time, $x = (Get-Date -UFormat %s) -split ','
        $minutes = [math]::Floor(([int]$cur_time - [int]$last_log_entry) / 60)
        ######### LOG #########


        return @{
            RegisterRunning = $reg_running
            TaskSchedulerRunning = $sched_job
            RegisterLog = $minutes
            SID = $pc.SID                       # because of upsert in savePcDetails
            Name = $pc.Name
        }

    }
}