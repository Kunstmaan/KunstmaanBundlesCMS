<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContext;

use Kunstmaan\AdminBundle\Entity\LogItem;
use Kunstmaan\AdminBundle\Entity\User;

/**
 * logout listener to log the logout
 */
class LoginListener
{
    private $context;
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
     * Do the magic.
     *
     * @param Event $event
     */
    public function onSecurityInteractiveLogin(Event $event)
    {
        /* @var User $user */
        $user = $this->context->getToken()->getUser();

        $logItem = new LogItem();
        $logItem->setStatus("info");
        $logItem->setUser($user);
        $logItem->setMessage($user . " succesfully logged in to the cms");
        $this->em->persist($logItem);
        $this->em->flush();
    }
}
