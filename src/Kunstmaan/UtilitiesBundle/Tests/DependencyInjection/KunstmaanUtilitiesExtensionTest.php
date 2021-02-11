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

    /**
     * @group legacy
     * @expectedDeprecation Setting the "kunstmaan_utilities.cipher.secret" parameter is deprecated since KunstmaanUtilitiesBundle 5.2, this value will be ignored/overwritten in KunstmaanUtilitiesBundle 6.0. Use the "kunstmaan_utilities.cipher.secret" config instead if you want to set a different value than the default "%kernel.secret%".
     */
    public function testLegacyParameterSecretParameter()
    {
        $this->setParameter('kunstmaan_utilities.cipher.secret', 'testvalue');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_utilities.cipher.secret', 'testvalue');
    }
}
