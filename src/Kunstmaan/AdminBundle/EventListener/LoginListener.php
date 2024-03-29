<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Login listener to log login actions
 */
class LoginListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var VersionChecker
     */
    private $versionChecker;

    public function __construct(LoggerInterface $logger, VersionChecker $versionChecker)
    {
        $this->logger = $logger;
        $this->versionChecker = $versionChecker;
    }

    /**
     * Handle login event.
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /* @var BaseUser $user */
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserInterface) {
            $this->logger->info(sprintf('%s successfully logged in to the cms', method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername()));
            $this->versionChecker->periodicallyCheck();
        }
    }
}
