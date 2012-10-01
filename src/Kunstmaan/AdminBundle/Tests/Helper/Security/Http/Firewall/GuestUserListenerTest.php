<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Http\Firewall;

use Kunstmaan\AdminBundle\Helper\Security\Http\Firewall\GuestUserListener;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * GuestUserListenerTest
 */
class GuestUserListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Http\Firewall\GuestUserListener::__construct
     * @covers Kunstmaan\AdminBundle\Helper\Security\Http\Firewall\GuestUserListener::handle
     */
    public function testHandleWithContextHavingAToken()
    {
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));
        $context
            ->expects($this->never())
            ->method('setToken');

        /* @var $provider UserProviderInterface */
        $provider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');

        /* @var $context SecurityContextInterface */
        $listener = new GuestUserListener($context, $provider, 'TheKey');
        /* @var $responseEvent GetResponseEvent */
        $responseEvent = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $listener->handle($responseEvent);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Http\Firewall\GuestUserListener::__construct
     * @covers Kunstmaan\AdminBundle\Helper\Security\Http\Firewall\GuestUserListener::handle
     */
    public function testHandleWithContextHavingNoToken()
    {
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue(null));
        $context->expects($this->once())
                ->method('setToken')
                ->with(
                    self::logicalAnd($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken'),
                        $this->attributeEqualTo('key', 'TheKey')
                    )
                );

        $account = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $account
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array('guest')));

        $provider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $provider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($this->equalTo('guest'))
            ->will($this->returnValue($account));

        /*
         * @var $context SecurityContextInterface
         * @var $provider UserProviderInterface
         */
        $listener = new GuestUserListener($context, $provider, 'TheKey');
        /* @var $responseEvent GetResponseEvent */
        $responseEvent = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $listener->handle($responseEvent);
    }

}
