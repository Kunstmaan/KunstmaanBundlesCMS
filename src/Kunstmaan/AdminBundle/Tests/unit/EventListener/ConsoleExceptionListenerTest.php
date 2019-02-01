<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Exception;
use Kunstmaan\AdminBundle\Command\ApplyAclCommand;
use Kunstmaan\AdminBundle\EventListener\ConsoleExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Kernel;

class ConsoleExceptionListenerTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testListener()
    {
        if (Kernel::VERSION_ID >= 40000) {
            static::markTestSkipped('Skipping legacy test');
        }

        $logger = $this->createMock(LoggerInterface::class);
        $listener = new ConsoleExceptionListener($logger);
        $event = $this->createMock(ConsoleExceptionEvent::class);

        $command = new ApplyAclCommand();
        $exception = new Exception();

        $logger->expects($this->once())->method('error')->willReturn(true);

        $event->expects($this->once())->method('getCommand')->willReturn($command);
        $event->expects($this->once())->method('getException')->willReturn($exception);

        $listener->onConsoleException($event);
    }
}
