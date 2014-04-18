<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Monolog\Logger;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Login listener to log login actions
 */
class LoginListener
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var VersionChecker
     */
    private $versionChecker;

    /**
     * Constructor
     *
     * @param Logger         $logger         The logger
     * @param VersionChecker $versionChecker The version checker
     */
    public function __construct(Logger $logger, VersionChecker $versionChecker)
    {
        $this->logger         = $logger;
        $this->versionChecker = $versionChecker;
    }

    /**
     * Handle login event.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /* @var BaseUser $user */
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserInterface) {
            $this->logger->addInfo($user . ' successfully logged in to the cms');
            $this->versionChecker->periodicallyCheck();
        }
    }
}
