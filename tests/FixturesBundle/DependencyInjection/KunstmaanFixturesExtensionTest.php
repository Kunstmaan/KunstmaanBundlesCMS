<?php

namespace Tests\Kunstmaan\FixturesBundle\DependencyInjection;

use Kunstmaan\FixturesBundle\DependencyInjection\KunstmaanFixturesExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tests\Kunstmaan\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanFixturesExtensionTest
 */
class KunstmaanFixturesExtensionTest extends AbstractPrependableExtensionTestCase
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
