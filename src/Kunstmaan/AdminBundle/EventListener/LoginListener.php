<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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

    /** @var EntityManagerInterface */
    private $em;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger         The logger
     * @param VersionChecker  $versionChecker The version checker
     */
    public function __construct(LoggerInterface $logger, VersionChecker $versionChecker, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->versionChecker = $versionChecker;
        $this->em = $em;
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
            $this->logger->info($user.' successfully logged in to the cms');
            $this->versionChecker->periodicallyCheck();
            $user->setLastLogin(new \DateTime());
            $this->em->flush();
        }
    }
}
