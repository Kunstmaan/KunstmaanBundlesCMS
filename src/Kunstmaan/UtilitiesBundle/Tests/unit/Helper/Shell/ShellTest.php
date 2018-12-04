<?php

namespace Tests\Kunstmaan\UtilitiesBundle\Helper\Shell;

use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;

/**
 * Class ShellTest
 */
class ShellTest extends \PHPUnit_Framework_TestCase
{
    public function testShellFunctionality()
    {
        $shell = new Shell();
        $pid = $shell->runInBackground('sleep 10');
        $this->assertTrue($shell->isRunning($pid));
        $shell->kill($pid);
        $this->assertFalse($shell->isRunning($pid));
        $pid = $shell->runInBackground('sleep 10', 10);
        $this->assertTrue($shell->isRunning($pid));
        $shell->kill($pid);
        $this->assertFalse($shell->isRunning($pid));
        $this->assertFalse($shell->kill(99999));
    }
}
