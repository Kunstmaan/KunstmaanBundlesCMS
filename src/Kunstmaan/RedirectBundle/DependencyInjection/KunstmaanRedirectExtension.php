<?php

namespace Kunstmaan\RedirectBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanRedirectExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('kunstmaan_redirect.redirect.class', $config['redirect_entity']);

        $enableImprovedRouter = $config['enable_improved_router'] ?? false;
        $container->setParameter('.kunstmaan_redirect.enable_improved_router', $enableImprovedRouter);

        if (!$enableImprovedRouter) {
            trigger_deprecation('kunstmaan/redirect-bundle', '6.3', 'Not setting the "kunstmaan_redirect.enable_improved_router" config to true is deprecated, it will always be true in 7.0.');
        }

        $container->findDefinition('kunstmaan_redirect.redirectrouter')->addMethodCall('enableImprovedRouter', [$enableImprovedRouter]);
    }
}
