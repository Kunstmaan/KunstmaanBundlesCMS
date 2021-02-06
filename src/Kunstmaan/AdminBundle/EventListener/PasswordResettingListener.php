<?php

namespace Kunstmaan\AdminBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserManager as FOSUserManager;
use Kunstmaan\AdminBundle\Event\ChangePasswordSuccessEvent;
use Kunstmaan\AdminBundle\Service\UserManager;

class PasswordResettingListener
{
    /** @var UserManager */
    private $userManager;

    public function __construct(/* UserManager */ $userManager)
    {
        if (!$userManager instanceof UserManager && !$userManager instanceof FOSUserManager) {
            throw new \InvalidArgumentException(sprintf('The "$userManager" argument must be of type "%s" or type "%s"', UserManager::class, FOSUserManager::class));
        }
        if ($userManager instanceof FOSUserManager) {
            // NEXT_MAJOR set the $userManager typehint to the kunstmaan usermanager.
            @trigger_error(sprintf('Passing the FOSUserBundle UserManager service as the "$userManager" argument of "%s" is deprecated since KunstmaanAdminBundle 5.8 and will be removed in KunstmaanAdminBundle 6.0. Use the "%s" service instead.', __METHOD__, UserManager::class), E_USER_DEPRECATED);
        }
        $this->userManager = $userManager;
    }

    /**
     * @deprecated Using the FosUser FilterUserResponseEvent is deprecated in KunstmaanNodeBundle 5.8 and will be removed in KunstmaanNodeBundle 6.0. Use the "Kunstmaan\AdminBundle\Event\ChangePasswordSuccessEvent" instead.
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
