<?php

namespace Kunstmaan\TaggingBundle\Tests\DependencyInjection;

use Kunstmaan\TaggingBundle\DependencyInjection\KunstmaanTaggingExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanTaggingExtensionTest
 */
class KunstmaanTaggingExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanTaggingExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('empty_extension', true);
        $this->load();

        $this->assertContainerBuilderHasParameter('empty_extension', true);
        $this->assertEquals('kunstmaan_tagging', $this->getContainerExtensions()[0]->getAlias());
    }
}
