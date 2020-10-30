<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\LeadGenerationBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
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
     * @expectedDeprecation Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanLeadGenerationBundle 5.2 and will be removed in KunstmaanLeadGenerationBundle 6.0. Use service decoration or a service alias instead.
     */
    public function testServiceClassParameterOverride()
    {
        $this->setParameter('kunstmaan_lead_generation.popup.twig.extension.class', 'Custom\Class');

        $this->compile();
    }
}
