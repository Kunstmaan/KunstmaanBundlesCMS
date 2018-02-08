<?php

namespace Kunstmaan\LeadGenerationBundle\DependencyInjection;

use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleBlackListAdminType;
use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleWhiteListAdminType;
use Kunstmaan\LeadGenerationBundle\Service\MenuAdaptor;
use Kunstmaan\LeadGenerationBundle\Service\PopupManager;
use Kunstmaan\LeadGenerationBundle\Service\Rule\LocaleRuleService;
use Kunstmaan\LeadGenerationBundle\Twig\PopupTwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanLeadGenerationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('kunstmaan_lead_generation.popup_types', $config['popup_types']);
        $container->setParameter('kunstmaan_lead_generation.debug', $config['debug']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_lead_generation.popup.manager' => new Alias(PopupManager::class),
                'kunstmaan_lead_generation.popup.twig.extension' => new Alias(PopupTwigExtension::class),
                'kunstmaan_lead_generation.menu.adaptor' => new Alias(MenuAdaptor::class),
                'kunstmaan_lead_generation.rule.form.localewhitelistrule' => new Alias(LocaleWhiteListAdminType::class),
                'kunstmaan_lead_generation.rule.form.localeblacklistrule' => new Alias(LocaleBlackListAdminType::class),
                'kunstmaan_lead_generation.rule.service.localeruleservice' => new Alias(LocaleRuleService::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_lead_generation.popup.twig.extension.class', PopupTwigExtension::class, true],
                ['kunstmaan_lead_generation.popup.manager.class', PopupManager::class, true],
                ['kunstmaan_lead_generation.menu.adaptor.class', MenuAdaptor::class, true],
            ]
        );
        // === END ALIASES ====
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $aliases
     */
    private function addParameteredAliases(ContainerBuilder $container, $aliases)
    {
        foreach ($aliases as $alias) {
            // Don't allow service with same name as class.
            if ($container->getParameter($alias[0]) !== $alias[1]) {
                $container->setAlias(
                    $container->getParameter($alias[0]),
                    new Alias($alias[1], $alias[2])
                );
            }
        }
    }
}
