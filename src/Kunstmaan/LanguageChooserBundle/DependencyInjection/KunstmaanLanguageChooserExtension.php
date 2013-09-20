<?php

namespace Kunstmaan\LanguageChooserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanLanguageChooserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('kunstmaan_language_chooser.autodetectlanguage', $config['autodetectlanguage']);
        $container->setParameter('kunstmaan_language_chooser.showlanguagechooser', $config['showlanguagechooser']);
        $container->setParameter('kunstmaan_language_chooser.languagechoosertemplate', $config['languagechoosertemplate']);
        $container->setParameter('kunstmaan_language_chooser.languagechooserlocales', $config['languagechooserlocales']);

        $luneticsLocaleConfig['allowed_locales'] = $config['languagechooserlocales'];
        $luneticsLocaleConfig['cookie']['set_on_change'] = true;
        $luneticsLocaleConfig['cookie']['secure'] = true;
        $luneticsLocaleConfig['guessing_order'] = array('query', 'router', 'kuma_url_guesser', 'cookie', 'session', 'browser');
        $container->prependExtensionConfig('lunetics_locale', $luneticsLocaleConfig);
    }

}
