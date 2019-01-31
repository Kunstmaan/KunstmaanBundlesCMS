<?php

namespace Kunstmaan\LeadGenerationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class DeprecateClassParametersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $expectedValues = [
            'kunstmaan_lead_generation.popup.twig.extension.class' => \Kunstmaan\LeadGenerationBundle\Twig\PopupTwigExtension::class,
            'kunstmaan_lead_generation.popup.manager.class' => \Kunstmaan\LeadGenerationBundle\Service\PopupManager::class,
            'kunstmaan_lead_generation.menu.adaptor.class' => \Kunstmaan\LeadGenerationBundle\Service\MenuAdaptor::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanLeadGenerationBundle 5.2 and will be removed in KunstmaanLeadGenerationBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}
