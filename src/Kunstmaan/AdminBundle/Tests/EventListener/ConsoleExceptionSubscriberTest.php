<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\EventListener\ConsoleExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleExceptionSubscriberTest extends TestCase
{
    public function testListener()
    {
        // Remove when sf3.4 support is removed
        if (!class_exists(ConsoleErrorEvent::class)) {
            // Nothing to test
            return;
        }

        $error = new \TypeError('An error occurred');
        $output = $this->createMock(OutputInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('critical');

        $subscriber = new ConsoleExceptionSubscriber($logger);
        $subscriber->onConsoleError(new ConsoleErrorEvent(new ArgvInput(['console.php', 'test:run', '--foo=baz', 'buzz']), $output, $error, new Command('test:run')));
    }
}
