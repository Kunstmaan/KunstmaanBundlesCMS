<?php

namespace Kunstmaan\RedirectBundle\DependencyInjection;

use Kunstmaan\RedirectBundle\Entity\Redirect;
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

        $this->addRedirectClassParameter($container, $config);
    }

    private function addRedirectClassParameter(ContainerBuilder $container, array $config)
    {
        if ($container->hasParameter('kunstmaan_redirect.redirect.class') && $container->getParameter('kunstmaan_redirect.redirect.class') !== Redirect::class) {
            @trigger_error('Overriding the redirect entity class with the "kunstmaan_redirect.redirect.class" parameter is deprecated since KunstmaanRedirectBundle 5.9 and will not be allowed in KunstmaanRedirectBundle 6.0. Use the "kunstmaan_redirect.redirect_entity" config option instead.', E_USER_DEPRECATED);

            return;
        }

        $container->setParameter('kunstmaan_redirect.redirect.class', $config['redirect_entity']);
    }
}
