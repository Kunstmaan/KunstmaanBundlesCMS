<?php

namespace Kunstmaan\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter(
            'twig.form.resources',
            array_merge(
                $container->getParameter('twig.form.resources'),
                array('KunstmaanMediaBundle:Form:formWidgets.html.twig')
            )
        );
        $container->setParameter('kunstmaan_media.soundcloud_api_key', $config['soundcloud_api_key']);
        $container->setParameter('kunstmaan_media.remote_video', $config['remote_video']);
        $container->setParameter('kunstmaan_media.enable_pdf_preview', $config['enable_pdf_preview']);
        $container->setParameter('kunstmaan_media.blacklisted_extensions', $config['blacklisted_extensions']);
        $container->setParameter('kunstmaan_media.web_root', $config['web_root']);
        $container->setParameter('kunstmaan_media.full_media_path', $config['web_root'] . '%kunstmaan_media.media_path%');

        $loader->load('services.yml');
        $loader->load('handlers.yml');

        if ($config['enable_pdf_preview'] === true) {
            $loader->load('pdf_preview.yml');
        }

        $container->setParameter('liip_imagine.filter.loader.background.class', 'Kunstmaan\MediaBundle\Helper\Imagine\BackgroundFilterLoader');
        $container->setParameter('liip_imagine.cache.manager.class', 'Kunstmaan\MediaBundle\Helper\Imagine\CacheManager');
        $container->setParameter('liip_imagine.cache.resolver.web_path.class', 'Kunstmaan\MediaBundle\Helper\Imagine\WebPathResolver');
        $container->setParameter('liip_imagine.controller.class', 'Kunstmaan\MediaBundle\Helper\Imagine\ImagineController');

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('imagine.xml');
    }

    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasParameter('kunstmaan_media.upload_dir')) {
            $container->setParameter('kunstmaan_media.upload_dir', '/uploads/media/');
        }

        $twigConfig = array();
        $twigConfig['globals']['upload_dir'] = $container->getParameter('kunstmaan_media.upload_dir');
        $twigConfig['globals']['mediabundleisactive'] = true;
        $twigConfig['globals']['mediamanager'] = '@kunstmaan_media.media_manager';
        $container->prependExtensionConfig('twig', $twigConfig);

        $liipConfig = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/imagine_filters.yml'));
        $container->prependExtensionConfig('liip_imagine', $liipConfig['liip_imagine']);

        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'kunstmaan_media';
    }
}
