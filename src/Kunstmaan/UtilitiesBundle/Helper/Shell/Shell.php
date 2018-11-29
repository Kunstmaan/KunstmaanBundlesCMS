<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Shell;

/**
 * A wrapper class which makes it possible to execute shell commands in the background.
 */
class Shell implements ShellInterface
{
    /**
     * @param string $command  The command
     * @param int    $priority The priority
     *
     * @return string The process id
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
     * @return bool
     *
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function isRunning($pid)
    {
        exec("ps $pid", $processState);

        return count($processState) >= 2;
    }

    /**
     * BE AWARE: when you use this method, make sure you don't use for example a http parameter as $pid because then you have a security hole !!!
     *
     * @param int $pid
     *
     * @return bool true when the process was successfully killed, false when the process wasn't running
     *
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function kill($pid)
    {
        if ($this->isRunning($pid)) {
            exec("kill -KILL $pid");

            return true;
        }

        return false;
    }
}
