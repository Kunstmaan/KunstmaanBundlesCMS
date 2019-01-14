<?php

namespace Kunstmaan\PagePartBundle\Tests\DependencyInjection;

use Kunstmaan\PagePartBundle\DependencyInjection\KunstmaanPagePartExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanPagePartExtensionTest
 */
class KunstmaanPagePartExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanPagePartExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_page_part.page_parts_presets');
        $this->assertContainerBuilderHasParameter('kunstmaan_page_part.page_templates_presets');
    }
}
