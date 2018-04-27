<?php

namespace Kunstmaan\AdminBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Set password_changed property to 1 after changing the password
 */
class PasswordResettingListener
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function onPasswordResettingSuccess(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setPasswordChanged(true);
        $this->userManager->updateUser($user);
    }
}
