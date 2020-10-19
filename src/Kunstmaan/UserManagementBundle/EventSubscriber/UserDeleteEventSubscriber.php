<?php

namespace Kunstmaan\UserManagementBundle\EventSubscriber;

use Kunstmaan\UserManagementBundle\Event\AfterUserDeleteEvent;
use Kunstmaan\UserManagementBundle\Event\UserEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserDeleteEventSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvents::AFTER_USER_DELETE => 'auditLogUserDelete',
        ];
    }

    public function auditLogUserDelete(AfterUserDeleteEvent $event)
    {
        $this->logger->info(sprintf('User "%s" was deleted by "%s"', $event->getDeletedUser(), $event->getDeletedBy()));
    }
}
