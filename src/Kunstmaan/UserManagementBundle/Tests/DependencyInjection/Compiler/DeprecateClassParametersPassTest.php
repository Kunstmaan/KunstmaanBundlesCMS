<?php

namespace Kunstmaan\UserManagementBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\UserManagementBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeprecateClassParametersPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }

    /**
     * @group legacy
     * @expectedDeprecation Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanUserManagementBundle 5.2 and will be removed in KunstmaanUserManagementBundle 6.0. Use service decoration or a service alias instead.
     */
    public function testServiceClassParameterOverride()
    {
        $this->setParameter('kunstmaan_user_management.menu.adaptor.class', 'Custom\Class');

        $this->compile();
    }
}
