<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ConsoleExceptionSubscriber.
 */
final class ConsoleExceptionSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * ConsoleExceptionListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::ERROR => 'onConsoleError',
        ];
    }

    /**
     * @param ConsoleErrorEvent $event
     */
    public function onConsoleError(ConsoleErrorEvent $event)
    {
        $command = $event->getCommand();
        $error = $event->getError();

        if (null !== $command) {
            $this->logCommandError($command, $error);
        }
    }

    /**
     * @param $command
     * @param $error
     */
    private function logCommandError($command, $error)
    {
        $message = sprintf(
            '%s: %s (uncaught error) at %s line %s while running console command `%s`',
            get_class($error),
            $error->getMessage(),
            $error->getFile(),
            $error->getLine(),
            $command->getName()
        );
        $this->logger->error($message, ['error' => $error]);
    }
}
