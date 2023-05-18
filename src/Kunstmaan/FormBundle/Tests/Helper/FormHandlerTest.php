<?php

namespace Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\FormBundle\Helper\FormHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FormHandlerTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testConstructorDeprecation()
    {
        $this->expectDeprecation('Since kunstmaan/form-bundle 6.3: Not passing the required services to "Kunstmaan\FormBundle\Helper\FormHandler::__construct" is deprecated and those parameters will be required in 7.0. Injected the required services in the constructor instead.');

        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container
            ->expects($this->exactly(4))
            ->method('get')
            ->will($this->onConsecutiveCalls(
                $this->createMock(EntityManagerInterface::class),
                $this->createMock(FormFactoryInterface::class),
                $this->createMock(RouterInterface::class),
                $this->createMock(EventDispatcherInterface::class),
            ))
        ;

        new FormHandler($container);
    }
}
