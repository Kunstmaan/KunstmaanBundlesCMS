<?php
namespace Kunstmaan\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanMediaExtension extends Extension implements PrependExtensionInterface
{

    /**
     * Loads configuration
     *
     * @param array            $configs   Configuration
     * @param ContainerBuilder $container Container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        if(!$container->hasParameter('kunstmaan_media.upload_dir')) {
            $container->setParameter('kunstmaan_media.upload_dir', '/uploads/media/');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            array('KunstmaanMediaBundle:Form:formWidgets.html.twig')
        ));

        $loader->load('services.yml');
        $loader->load('handlers.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $twigConfig['globals']['upload_dir'] = $container->getParameter('kunstmaan_media.upload_dir');
        $twigConfig['globals']['mediabundleisactive'] = true;
        $twigConfig['globals']['mediamanager'] = "@kunstmaan_media.media_manager";
        $container->prependExtensionConfig('twig', $twigConfig);

        $liipConfig = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/liip_imagine.yml'));
        $container->prependExtensionConfig('liip_imagine', $liipConfig['liip_imagine']);

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'kunstmaan_media';
    }
}