<?php

namespace Kunstmaan\PagePartBundle\Tests\DependencyInjection;

use Kunstmaan\PagePartBundle\DependencyInjection\KunstmaanPagePartExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanPagePartExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
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
