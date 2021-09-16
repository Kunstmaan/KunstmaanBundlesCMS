<?php

namespace Kunstmaan\UtilitiesBundle\Tests\DependencyInjection;

use Kunstmaan\UtilitiesBundle\DependencyInjection\KunstmaanUtilitiesExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanUtilitiesExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanUtilitiesExtension()];
    }

    public function testCorrectDefaultParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_utilities.cipher.secret', '%kernel.secret%');
    }

    public function testParameterWithSecretParameter()
    {
        $this->setParameter('secret', 'testvalue');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_utilities.cipher.secret', 'testvalue');
    }
}
