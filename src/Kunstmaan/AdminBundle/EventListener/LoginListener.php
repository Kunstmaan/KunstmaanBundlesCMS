<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Monolog\Logger;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Kunstmaan\AdminBundle\Entity\LogItem;
use Kunstmaan\AdminBundle\Entity\User;

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
     * Constructor
     *
     * @param SecurityContext $context The security context
     * @param EntityManager   $em      The entity manager
     * @param Logger          $logger  The logger
     */
    public function __construct(SecurityContext $context, EntityManager $em, Logger $logger)
    {
        $this->context = $context;
        $this->em      = $em;
        $this->logger  = $logger;
    }

    /**
     * Handle login event.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /* @var User $user */
        $user = $this->context->getToken()->getUser();

        if ($user instanceof UserInterface) {
            $this->logger->addInfo($user . " succesfully logged in to the cms");
        }
    }
}
