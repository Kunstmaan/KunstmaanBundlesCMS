<?php

namespace Kunstmaan\FixturesBundle\Tests\DependencyInjection;

use Kunstmaan\FixturesBundle\DependencyInjection\KunstmaanFixturesExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class KunstmaanFixturesExtensionTest
 */
class KunstmaanFixturesExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanFixturesExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('empty_extension', true);
        $this->load();

        $this->assertContainerBuilderHasParameter('empty_extension', true);
    }
}
