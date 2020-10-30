<?php

namespace Kunstmaan\MultiDomainBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\MultiDomainBundle\DependencyInjection\CompilerPass\DeprecateClassParametersPass;
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
     * @expectedDeprecation Using the "kunstmaan_multi_domain.domain_configuration.class" parameter to change the class of the service definition is deprecated in KunstmaanMultiDomainBundle 5.2 and will be removed in KunstmaanMultiDomainBundle 6.0. Use service decoration or a service alias instead.
     */
    public function testServiceClassParameterOverride()
    {
        $this->setParameter('kunstmaan_multi_domain.domain_configuration.class', 'Custom\Class');

        $this->compile();
    }
}
