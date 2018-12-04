<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;

/**
 * Class ConsoleExceptionListener.
 *
 * @deprecated in KunstmaanAdminBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.
 */
class ConsoleExceptionListener
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
     * @param ConsoleExceptionEvent $event
     */
    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        // if the newer error event exists, don't bother with the old exception, our subscriber will handle this
        if (class_exists(ConsoleErrorEvent::class)) {
            return;
        }

        $command = $event->getCommand();
        $exception = $event->getException();

        $this->logCommandError($command, $exception);
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
