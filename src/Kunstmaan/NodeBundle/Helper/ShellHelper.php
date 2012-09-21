<?php

namespace Kunstmaan\NodeBundle\Helper;

/**
 * ShellHelper
 */
class ShellHelper
{

    /**
     * @param string $command  The command
     * @param int    $priority The priority
     *
     * @return string
     */
    public function runInBackground($command, $priority = 0)
    {
        if ($priority) {
            $pid = shell_exec("nohup nice -n $priority $command > /dev/null & echo $!");
        } else {
            $pid = shell_exec("nohup $command > /dev/null & echo $!");
        }

        return $pid;
    }

    /**
     * @param int $pid
     *
     * @return boolean
     */
    public function isProcessRunning($pid)
    {
        exec("ps $pid", $processState);

        return (count($processState) >= 2);
    }

    /**
     * BE AWARE: when you use this method, make sure you don't use for example a http parameter as $pid because then you have a security hole !!!
     *
     * @param int $pid
     *
     * @return boolean
     */
    public function kill($pid)
    {
        if ($this->isProcessRunning($pid)) {
            exec("kill -KILL $pid");

            return true;
        }

        return false;
    }

}
