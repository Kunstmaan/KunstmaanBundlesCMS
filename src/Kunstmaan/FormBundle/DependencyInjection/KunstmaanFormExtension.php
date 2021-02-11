<?php

namespace Kunstmaan\FormBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

class KunstmaanFormExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('kunstmaan_form.deletable_formsubmissions', $config['deletable_formsubmissions']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasParameter('form_submission_rootdir')) {
            $container->setParameter('form_submission_rootdir',
                sprintf('%s/%s/uploads/formsubmissions', $container->getParameter('kernel.project_dir'), Kernel::VERSION_ID >= 40000 ? 'public' : 'web'));
        }

        if (!$container->hasParameter('form_submission_webdir')) {
            $container->setParameter('form_submission_webdir', '/uploads/formsubmissions/');
        }

        $twigConfig['globals']['form_submission_webdir'] = $container->getParameter('form_submission_webdir');
        $container->prependExtensionConfig('twig', $twigConfig);
        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }
}
