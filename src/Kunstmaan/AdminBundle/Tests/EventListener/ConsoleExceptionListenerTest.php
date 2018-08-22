<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Exception;
use Kunstmaan\AdminBundle\Command\ApplyAclCommand;
use Kunstmaan\AdminBundle\EventListener\ConsoleExceptionListener;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;

class ConsoleExceptionListenerTest extends PHPUnit_Framework_TestCase
{
    public function testListener()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')->willReturn(true);
        $listener = new ConsoleExceptionListener($logger);

        $command = new ApplyAclCommand();
        $exception = new Exception();

        $event = $this->createMock(ConsoleExceptionEvent::class);
        $event->expects($this->once())->method('getCommand')->willReturn($command);
        $event->expects($this->once())->method('getException')->willReturn($exception);

        $listener->onConsoleException($event);
    }
}