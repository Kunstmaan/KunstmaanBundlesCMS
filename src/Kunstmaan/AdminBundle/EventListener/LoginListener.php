<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Monolog\Logger;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Login listener to log login actions
 */
class LoginListener
{
    /**
     * @var SecurityContext $context
     */
    private $context;

    /**
     * @var EntityManager $em
     */
    private $em;

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
     * @param SecurityContext $context        The security context
     * @param EntityManager   $em             The entity manager
     * @param Logger          $logger         The logger
     * @param $versionChecker $versionChecker The version checker
     */
    public function __construct(SecurityContext $context, EntityManager $em, Logger $logger, VersionChecker $versionChecker)
    {
        $this->context        = $context;
        $this->em             = $em;
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
            $this->logger->addInfo($user . " succesfully logged in to the cms");
            $this->versionChecker->periodicallyCheck();
        }
    }
}
