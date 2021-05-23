<?php

namespace Kunstmaan\AdminBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserManager;

/**
 * @deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0. The listener logic is already executed in the `\Kunstmaan\AdminBundle\Service\UserManager::updatePassword` method.
 */
class PasswordResettingListener
{
    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @deprecated Using the FosUser FilterUserResponseEvent is deprecated in KunstmaanNodeBundle 5.9 and will be removed in KunstmaanNodeBundle 6.0. Use the "Kunstmaan\AdminBundle\Event\ChangePasswordSuccessEvent" instead.
     */
    public function onPasswordResettingSuccess(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setPasswordChanged(true);
        $this->userManager->updateUser($user);
    }
}
