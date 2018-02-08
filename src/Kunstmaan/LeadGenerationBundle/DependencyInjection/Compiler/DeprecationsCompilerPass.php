<?php

namespace Kunstmaan\LeadGenerationBundle\DependencyInjection\Compiler;

use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleBlackListAdminType;
use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleWhiteListAdminType;
use Kunstmaan\LeadGenerationBundle\Service\MenuAdaptor;
use Kunstmaan\LeadGenerationBundle\Service\PopupManager;
use Kunstmaan\LeadGenerationBundle\Service\Rule\LocaleRuleService;
use Kunstmaan\LeadGenerationBundle\Twig\PopupTwigExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\LeadGenerationBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_lead_generation.popup.manager', PopupManager::class],
                ['kunstmaan_lead_generation.popup.twig.extension', PopupTwigExtension::class],
                ['kunstmaan_lead_generation.menu.adaptor', MenuAdaptor::class],
                ['kunstmaan_lead_generation.rule.form.localewhitelistrule', LocaleWhiteListAdminType::class],
                ['kunstmaan_lead_generation.rule.form.localeblacklistrule', LocaleBlackListAdminType::class],
                ['kunstmaan_lead_generation.rule.service.localeruleservice', LocaleRuleService::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_lead_generation.popup.twig.extension.class', PopupTwigExtension::class, true],
                ['kunstmaan_lead_generation.popup.manager.class', PopupManager::class, true],
                ['kunstmaan_lead_generation.menu.adaptor.class', MenuAdaptor::class, true],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanLeadGenerationBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanLeadGenerationBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}
