<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\DependencyInjection;

use Kunstmaan\LeadGenerationBundle\DependencyInjection\KunstmaanLeadGenerationExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanLeadGenerationExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanLeadGenerationExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load([
            'popup_types' => ['abc'],
            'debug' => true,
         ]);

        $this->assertContainerBuilderHasParameter('kunstmaan_lead_generation.popup_types');
        $this->assertContainerBuilderHasParameter('kunstmaan_lead_generation.debug', true);
        $this->assertContainerBuilderHasParameter('kunstmaan_lead_generation.popup.twig.extension.class', 'Kunstmaan\LeadGenerationBundle\Twig\PopupTwigExtension');
        $this->assertContainerBuilderHasParameter('kunstmaan_lead_generation.popup.manager.class', 'Kunstmaan\LeadGenerationBundle\Service\PopupManager');
        $this->assertContainerBuilderHasParameter('kunstmaan_lead_generation.menu.adaptor.class', 'Kunstmaan\LeadGenerationBundle\Service\MenuAdaptor');
    }
}
