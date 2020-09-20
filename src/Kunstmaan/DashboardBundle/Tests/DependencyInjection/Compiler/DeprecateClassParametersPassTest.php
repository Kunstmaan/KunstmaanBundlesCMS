<?php

namespace Kunstmaan\DashboardBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\DashboardBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeprecateClassParametersPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }

    /**
     * @group legacy
     * @expectedDeprecation Using the "kunstmaan_dashboard.googleclient.class" parameter to change the class of the service definition is deprecated in KunstmaanDashboardBundle 5.2 and will be removed in KunstmaanDashboardBundle 6.0. Use service decoration or a service alias instead.
     */
    public function testServiceClassParameterOverride()
    {
        $this->setParameter('kunstmaan_dashboard.googleclient.class', 'Custom\Class');

        $this->compile();
    }
}
