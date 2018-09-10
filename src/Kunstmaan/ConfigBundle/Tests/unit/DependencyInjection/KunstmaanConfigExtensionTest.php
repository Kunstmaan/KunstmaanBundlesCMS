<?php

namespace Kunstmaan\ConfigBundle\Tests\DependencyInjection;

use Kunstmaan\ConfigBundle\DependencyInjection\KunstmaanConfigExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanConfigExtensionTest
 */
class KunstmaanConfigExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanConfigExtension()];
    }


    public function testCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_config');
        $this->assertContainerBuilderHasParameter('kunstmaan_config.menu.adaptor.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_config.twig.extension.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_config.controller.config.class');
    }
}
