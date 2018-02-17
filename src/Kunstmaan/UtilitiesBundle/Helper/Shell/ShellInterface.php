<?php

declare(strict_types=1);

namespace Kunstmaan\UtilitiesBundle\Helper\Shell;

/**
 * Shell interface, classes which extends this interface will make it possible to execute shell commands in the background.
 */
interface ShellInterface
{

    /**
     * @param string $command The command
     *
     * @return int
     */
    public function runInBackground(string $command): int;

    /**
     * @param int $pid
     *
     * @return boolean
     */
    public function isRunning(int $pid): bool;

    /**
     * BE AWARE: when you use this method, make sure you don't use for example a http parameter as $pid because then you have a security hole !!!
     *
     * @param int $pid
     *
     * @return boolean
     */
    public function kill(int $pid): bool;

}
