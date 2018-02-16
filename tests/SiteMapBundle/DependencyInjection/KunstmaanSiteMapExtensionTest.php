<?php

namespace Tests\Kunstmaan\SiteMapBundle\DependencyInjection;

use Kunstmaan\SiteMapBundle\DependencyInjection\KunstmaanSiteMapExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tests\Kunstmaan\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanSiteMapExtensionTest
 */
class KunstmaanSiteMapExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanSiteMapExtension()];
    }


    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('empty_extension', true);
        $this->load();

        $this->assertContainerBuilderHasParameter('empty_extension', true );
    }
}
