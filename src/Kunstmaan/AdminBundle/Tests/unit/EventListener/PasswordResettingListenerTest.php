<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserManager as FosUserManager;
use Kunstmaan\AdminBundle\Service\UserManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Event\ChangePasswordSuccessEvent;
use Kunstmaan\AdminBundle\EventListener\PasswordResettingListener;
use PHPUnit\Framework\TestCase;

class PasswordResettingListenerTest extends TestCase
{
    public function testListener()
    {
        $manager = $this->createMock(FosUserManager::class);
        $user = $this->createMock(User::class);

        $manager->expects($this->once())->method('updateUser')->willReturn(true);
        $user->expects($this->once())->method('setPasswordChanged')->willReturn(true);

        $event = $this->createMock(FilterUserResponseEvent::class);
        $event->expects($this->any())->method('getUser')->willReturn($user);

        $listener = new PasswordResettingListener($manager);
        $listener->onPasswordResettingSuccess($event);
    }

    public function testListenerNewMethod()
    {
        $manager = $this->createMock(UserManager::class);
        $user = $this->createMock(User::class);

        $manager->expects($this->once())->method('updateUser')->willReturn(true);
        $user->expects($this->once())->method('setPasswordChanged')->willReturn(true);

        $event = $this->createMock(ChangePasswordSuccessEvent::class);
        $event->expects($this->any())->method('getUser')->willReturn($user);

        $listener = new PasswordResettingListener($manager);
        $listener->onPasswordResettingSuccessCMS($event);
    }
}
