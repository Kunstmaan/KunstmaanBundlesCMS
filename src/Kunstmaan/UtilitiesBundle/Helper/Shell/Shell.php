<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Shell;

use Symfony\Component\Process\Process;

/**
 * A wrapper class which makes it possible to execute shell commands in the background.
 */
class Shell implements ShellInterface
{

    /**
     * @param string $command The command
     * @deprecated int $priority
     *
     * @return int The process id
     *
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function runInBackground($command, $priority = 0)
    {
        $process = new Process($command);
        $process->disableOutput();
        $process->start();

        return $process->getPid();
    }

    /**
     * @param int $pid
     *
     * @return boolean
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function isRunning($pid)
    {
        $process = new Process(
            sprintf('ps -p %s -o pid', $pid)
        );
        $process->run();

        $output = trim($process->getOutput());
        $processState = explode("\n", $output);

        return (2 >= count($processState));
    }

    /**
     * BE AWARE: when you use this method, make sure you don't use for example a http parameter as $pid because then you have a security hole !!!
     *
     * @param int $pid
     *
     * @return boolean true when the process was successfully killed, false when the process wasn't running.
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function kill($pid)
    {
        if ($this->isRunning($pid)) {
            $process = new Process(
                sprintf('kill -KILL %d', $pid)
            );
            $process->run();
            return true;
        }

        return false;
    }

}
