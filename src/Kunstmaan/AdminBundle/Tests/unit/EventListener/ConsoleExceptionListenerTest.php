<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Exception;
use Kunstmaan\AdminBundle\Command\ApplyAclCommand;
use Kunstmaan\AdminBundle\EventListener\ConsoleExceptionListener;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;

class ConsoleExceptionListenerTest extends PHPUnit_Framework_TestCase
{
    public function testListener()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $listener = new ConsoleExceptionListener($logger);

        $event = $this->createMock(ConsoleExceptionEvent::class);
        if (class_exists(ConsoleErrorEvent::class)) {
            $this->assertNull($listener->onConsoleException($event));
        } else {
            $command = new ApplyAclCommand();
            $exception = new Exception();

            $logger->expects($this->once())->method('error')->willReturn(true);

            $event->expects($this->once())->method('getCommand')->willReturn($command);
            $event->expects($this->once())->method('getException')->willReturn($exception);

            $listener->onConsoleException($event);
        }
    }
}
