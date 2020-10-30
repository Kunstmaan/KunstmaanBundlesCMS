<?php

namespace Kunstmaan\NodeSearchBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
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
     * @expectedDeprecation Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanNodeSearchBundle 5.2 and will be removed in KunstmaanNodeSearchBundle 6.0. Use service decoration or a service alias instead.
     */
    public function testServiceClassParameterOverride()
    {
        $this->setParameter('kunstmaan_node_search.search_configuration.node.class', 'Custom\Class');

        $this->compile();
    }
}
