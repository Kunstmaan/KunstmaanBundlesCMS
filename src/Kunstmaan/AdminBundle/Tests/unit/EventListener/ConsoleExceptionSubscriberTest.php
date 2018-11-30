<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Exception;
use Codeception\Test\Unit;
use Kunstmaan\AdminBundle\Command\ApplyAclCommand;
use Kunstmaan\AdminBundle\EventListener\ConsoleExceptionSubscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleExceptionSubscriberTest extends Unit
{
    public function testListener()
    {
        if (!class_exists(ConsoleErrorEvent::class)) {
            // Nothing to test
            return;
        }

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')->willReturn(true);
        $subscriber = new ConsoleExceptionSubscriber($logger);

        $command = new ApplyAclCommand();
        $exception = new Exception();

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        $event = new ConsoleErrorEvent($input, $output, $exception, $command);

        $subscriber->onConsoleError($event);
    }
}
