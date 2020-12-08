<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\UserProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserProcessorTest extends TestCase
{
    /**
     * @group legacy
     * @expectedDeprecation Passing the container as the first argument of "Kunstmaan\AdminBundle\Helper\UserProcessor" is deprecated in KunstmaanAdminBundle 5.4 and will be removed in KunstmaanAdminBundle 6.0. Inject the "security.token_storage" service instead.
     */
    public function testConstructorContainerDeprecation()
    {
        $container = $this->createMock(ContainerInterface::class);
        new UserProcessor($container);
    }
}
