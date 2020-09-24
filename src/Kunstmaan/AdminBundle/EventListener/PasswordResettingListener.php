<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Event\FilterUserResponseEvent;
use \FOS\UserBundle\Event\FilterUserResponseEvent as FosFilterUserResponseEvent;
use Kunstmaan\AdminBundle\Service\UserManager;

/**
 * Set password_changed property to 1 after changing the password
 */
class PasswordResettingListener
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param FilterUserResponseEvent $event
     * @deprecated Using the Fos FilterUserResponseEvent is deprecated in KunstmaanNodeBundle 5.8 and will be removed in KunstmaanNodeBundle 6.0. Use the Kunstmaan FilterUserResponseEvent instead.
     */
    public function onPasswordResettingSuccess(FosFilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setPasswordChanged(true);
        $this->userManager->updateUser($user);
    }

    public function onPasswordResettingSuccessCMS(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setPasswordChanged(true);
        $this->userManager->updateUser($user);
    }
}
