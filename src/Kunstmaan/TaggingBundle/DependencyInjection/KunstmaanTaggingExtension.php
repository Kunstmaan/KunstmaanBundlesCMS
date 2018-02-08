<?php

namespace Kunstmaan\TaggingBundle\DependencyInjection;

use Kunstmaan\TaggingBundle\Entity\TagManager;
use Kunstmaan\TaggingBundle\EventListener\CloneListener;
use Kunstmaan\TaggingBundle\EventListener\IndexNodeEventListener;
use Kunstmaan\TaggingBundle\EventListener\TagsListener;
use Kunstmaan\TaggingBundle\Form\TagsAdminType;
use Kunstmaan\TaggingBundle\Helper\Menu\TagMenuAdaptor;
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
class KunstmaanTaggingExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kuma_tagging.tag_manager' => new Alias(TagManager::class),
                'kuma_tagging.listener' => new Alias(TagsListener::class),
                'kuma_tagging.clone.listener' => new Alias(CloneListener::class),
                'kuma_tagging.index_node.listener' => new Alias(IndexNodeEventListener::class),
                'kuma_tagging.menu.adaptor' => new Alias(TagMenuAdaptor::class),
                'form.type.tags' => new Alias(TagsAdminType::class),
            ]
        );

        // === END ALIASES ====
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'kunstmaan_tagging';
    }
}
