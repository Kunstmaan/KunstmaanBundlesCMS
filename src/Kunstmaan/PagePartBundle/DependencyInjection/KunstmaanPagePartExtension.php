<?php

namespace Kunstmaan\PagePartBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class KunstmaanPagePartExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('kunstmaan_page_part.extended', $configs['extended_pagepart_chooser']);
        $container->setParameter('kunstmaan_page_part.page_parts_presets', $configs['pageparts']);
        $container->setParameter('kunstmaan_page_part.page_templates_presets', $configs['pagetemplates']);
    }
}
