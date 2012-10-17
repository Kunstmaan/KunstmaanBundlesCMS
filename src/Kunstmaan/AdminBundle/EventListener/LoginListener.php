<?php

namespace Kunstmaan\AdminBundle\EventListener;

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
    /* @var SecurityContext $context */
    private $context;

    /* @var EntityManager $em */
    private $em;

    /**
     * Constructor
     *
     * @param SecurityContext $context The security context
     * @param EntityManager   $em      The entity manager
     */
    public function __construct(SecurityContext $context, EntityManager $em)
    {
        $this->context = $context;
        $this->em      = $em;
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
            // @todo log $user . " succesfully logged in to the cms"
        }
    }
}
