<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Service\UserManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Kunstmaan\AdminBundle\Event\ChangePasswordSuccessEvent;

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
    public function __construct(/* UserManager */ $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param FilterUserResponseEvent $event
     * @deprecated Using the Fos FilterUserResponseEvent is deprecated in KunstmaanNodeBundle 5.8 and will be removed in KunstmaanNodeBundle 6.0. Use the Kunstmaan FilterUserResponseEvent instead.
     */
    public function onPasswordResettingSuccess(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setPasswordChanged(true);
        $this->userManager->updateUser($user);
    }

    public function onPasswordResettingSuccessCMS(ChangePasswordSuccessEvent $event)
    {
        $user = $event->getUser();
        $user->setPasswordChanged(true);
        $this->userManager->updateUser($user);
    }
}
