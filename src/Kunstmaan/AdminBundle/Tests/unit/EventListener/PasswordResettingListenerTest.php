<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\EventListener\PasswordResettingListener;
use PHPUnit_Framework_TestCase;

class PasswordResettingListenerTest extends PHPUnit_Framework_TestCase
{
    public function testListener()
    {
        $manager = $this->createMock(UserManager::class);
        $user = $this->createMock(User::class);

        $manager->expects($this->once())->method('updateUser')->willReturn(true);
        $user->expects($this->once())->method('setPasswordChanged')->willReturn(true);

        $event = $this->createMock(FilterUserResponseEvent::class);
        $event->expects($this->any())->method('getUser')->willReturn($user);

        $listener = new PasswordResettingListener($manager);
        $listener->onPasswordResettingSuccess($event);
    }
}
