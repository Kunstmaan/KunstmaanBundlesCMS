<?php

namespace Kunstmaan\RedirectBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\RedirectBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
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
     * @expectedDeprecation Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanRedirectBundle 5.2 and will be removed in KunstmaanRedirectBundle 6.0. Use service decoration or a service alias instead.
     */
    public function testServiceClassParameterOverride()
    {
        $this->setParameter('kunstmaan_redirect.menu.adaptor.class', 'Custom\Class');

        $this->compile();
    }
}
