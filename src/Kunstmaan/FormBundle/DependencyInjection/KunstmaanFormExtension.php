<?php

namespace Kunstmaan\FormBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanFormExtension extends Extension implements PrependExtensionInterface
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
    }

    public function prepend(ContainerBuilder $container)
    {
        if(!$container->hasParameter('form_submission_rootdir')) {
            $container->setParameter('form_submission_rootdir',
                sprintf('%s/../web/uploads/formsubmissions', $container->getParameter('kernel.root_dir')));
        }

        if(!$container->hasParameter('form_submission_webdir')) {
            $container->setParameter('form_submission_webdir', '/uploads/formsubmissions/');
        }

        $twigConfig['globals']['form_submission_webdir'] = $container->getParameter('form_submission_webdir');
        $container->prependExtensionConfig('twig', $twigConfig);
        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }
}
