<?php

namespace Kunstmaan\AdminNodeBundle\Helper;

class ShellHelper
{

    public function runInBackground($command, $priority = 0)
    {
        if ($priority) {
            $pid = shell_exec("nohup nice -n $priority $command > /dev/null & echo $!");
        } else {
            $pid = shell_exec("nohup $command > /dev/null & echo $!");
        }

        return $pid;
    }

    public function isProcessRunning($pid)
    {
        exec("ps $pid", $processState);

        return (count($processState) >= 2);
    }

    public function kill($pid)
    {
        if ($this->isProcessRunning($pid)) {
            exec("kill -KILL $pid");

            return true;
        }

        return false;
    }

}
