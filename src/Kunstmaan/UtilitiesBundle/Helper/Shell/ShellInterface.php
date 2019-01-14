<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Shell;

/**
 * Shell interface, classes which extends this interface will make it possible to execute shell commands in the background.
 */
interface ShellInterface
{
    /**
     * @param string $command  The command
     * @param int    $priority The priority
     *
     * @return string
     */
    public function runInBackground($command, $priority = 0);

    /**
     * @param int $pid
     *
     * @return bool
     */
    public function isRunning($pid);

    /**
     * BE AWARE: when you use this method, make sure you don't use for example a http parameter as $pid because then you have a security hole !!!
     *
     * @param int $pid
     *
     * @return bool
     */
    public function kill($pid);
}
