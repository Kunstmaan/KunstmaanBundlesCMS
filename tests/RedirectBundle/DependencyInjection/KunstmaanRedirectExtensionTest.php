<?php

namespace Tests\Kunstmaan\RedirectBundle\DependencyInjection;

use Kunstmaan\PagePartBundle\DependencyInjection\KunstmaanPagePartExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tests\Kunstmaan\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanRedirectExtensionTest
 * @package Tests\Kunstmaan\RedirectBundle\DependencyInjection
 */
class KunstmaanRedirectExtensionTest extends AbstractPrependableExtensionTestCase
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

        $this->assertContainerBuilderHasParameter('kunstmaan_page_part.page_parts_presets' );
        $this->assertContainerBuilderHasParameter('kunstmaan_page_part.page_templates_presets' );
    }
}
