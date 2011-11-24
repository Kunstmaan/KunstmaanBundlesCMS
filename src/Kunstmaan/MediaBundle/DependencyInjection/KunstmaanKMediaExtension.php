<?php

namespace Kunstmaan\KMediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

use Kunstmaan\KMediaBundle\Entity\MediaContext;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanKMediaExtension extends Extension
{
    protected $defaultCdn;
    protected $defaultFilesystem;
    protected $defaultPathGenerator;
    protected $defaultUuidGenerator;
    protected $defaultManipulator;
    protected $defaultProvider;

    /**
     * Loads configuration
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (array('provider', 'cdn', 'filesystem', 'generator', 'image', 'manager', 'orm') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $manager = $container->getDefinition('kunstmaan_k_media.manager');

        // Order matters !!
        $this->initCdns($config, $manager, $container);
        $this->initFilesystem($config, $manager, $container);
        $this->initGenerators($config, $manager, $container);
        $this->initManipulators($config, $manager, $container);
        $this->initProviders($config, $manager, $container);
        $this->initContexts($config, $manager, $container);

    }

    private function initCdns(array $config, Definition $manager, ContainerBuilder $container)
    {
        $cdnList = $config['cdn'];
        foreach($cdnList as $name => $options) {
            $id = $options['id'];
            if (!$container->hasDefinition($id)) {
                continue;
            }

            $this->remapParametersNamespaces($options, $container, array(
                'options'  => "$id.%s",
            ));

            $reference = new Reference($id);
            $manager->addMethodCall('addCdn', array($name, $reference));
            if ($options['default']) {
                $manager->addMethodCall('setDefaultCdn', array($reference));
                $this->defaultCdn = $id;
            }
        }
    }

    private function initFilesystem(array $config, Definition $manager, ContainerBuilder $container)
    {
        $filesystemList = $config['filesystem'];
        foreach($filesystemList as $name => $options) {
            $id = $options['id'];
            if (!$container->hasDefinition($id)) {
                continue;
            }

            $this->remapParametersNamespaces($options, $container, array(
                'options'  => "$id.%s",
            ));

            $reference = new Reference($id);
            $manager->addMethodCall('addFilesystem', array($name, $reference));
            if ($options['default']) {
                $manager->addMethodCall('setDefaultFilesystem', array($reference));
                $this->defaultFilesystem = $id;
            }
        }
    }

    private function initGenerators(array $config, Definition $manager, ContainerBuilder $container)
    {
        $pathGeneratorList = $config['generator']['path'];
        foreach($pathGeneratorList as $name => $options) {
            $id = $options['id'];
            if (!$container->hasDefinition($id)) {
                continue;
            }

            if ($options['default']) {
                $this->defaultPathGenerator = $id;
            }
        }

        $uuidGeneratorList = $config['generator']['uuid'];
        foreach($uuidGeneratorList as $name => $options) {
            $id = $options['id'];
            if (!$container->hasDefinition($id)) {
                continue;
            }

            if ($options['default']) {
                $this->defaultUuidGenerator = $id;
            }
        }
    }

    private function initManipulators(array $config, Definition $manager, ContainerBuilder $container)
    {
        $manipulatorList = $config['manipulator'];
        foreach($manipulatorList as $name => $options) {
            $id = $options['id'];
            if (!$container->hasDefinition($id)) {
                continue;
            }

            $this->remapParametersNamespaces($options, $container, array(
                'options'  => "$id.%s",
            ));

            // TODO: this is too hardcoded !
            if ($options['default']) {
                $this->defaultManipulator = $id;
            }
        }
    }

    private function initProviders(array $config, Definition $manager, ContainerBuilder $container)
    {
        $providerList = $config['provider'];
        foreach($providerList as $name => $options) {
            $id = $options['id'];
            if (!$container->hasDefinition($id)) {
                continue;
            }

            $this->remapParametersNamespaces($options, $container, array(
                'options'  => "$id.%s",
            ));

            $def = $container->getDefinition($id);
            $def->replaceArgument(0, $name);

            // CDN
            if (isset($options['cdn'])) {
                $cdn = $options['cdn'];
                if (isset($config['cdn'][$cdn])) {
                    $cdnId = $config['cdn'][$cdn]['id'];
                    $def->replaceArgument(1, new Reference($cdnId));
                }
            }
            else {
                $def->replaceArgument(1, new Reference($this->defaultCdn));
            }

            // Filesystem
            if (isset($options['filesystem'])) {
                $filesystem = $options['filesystem'];
                if (isset($config['filesystem'][$filesystem])) {
                    $filesystemId = $config['filesystem'][$filesystem]['id'];
                    $def->replaceArgument(2, new Reference($filesystemId));
                }
            }
            else {
                $def->replaceArgument(2, new Reference($this->defaultFilesystem));
            }

            // Generators
            // TODO: This is not dynamic ! to be fixed !
            $def->replaceArgument(3, new Reference($this->defaultPathGenerator));
            $def->replaceArgument(4, new Reference($this->defaultUuidGenerator));

            // Image manipulators
            if ($def->hasMethodCall('setImageManipulator')) {
                $def->removeMethodCall('setImageManipulator');
                $def->addMethodCall('setImageManipulator', array(new Reference($this->defaultManipulator)));
            }

            $reference = new Reference($id);
            $manager->addMethodCall('addProvider', array($name, $reference));
            if ($options['default']) {
                $manager->addMethodCall('setDefaultProvider', array($reference));
                $this->defaultProvider = $id;
            }
        }
    }

    private function initContexts(array $config, Definition $manager, ContainerBuilder $container)
    {
        $contextList = $config['contexts'];
        foreach($contextList as $name => $options) {
            $context = new Definition('Kunstmaan\KMediaBundle\Entity\MediaContext');
            $context->addArgument($name);

            // Provider
            $providerId = $this->defaultProvider;
            if (isset($options['provider'])) {
                $provider = $options['provider'];
                if (isset($config['provider'][$provider])) {
                    $providerId = $config['provider'][$provider]['id'];
                    $context->addMethodCall('setProvider', array(new Reference($providerId)));
                }
            }
            else {
                $context->addMethodCall('setProvider', array(new Reference($providerId)));
            }

            // Formats
            foreach($options['formats'] as $formatName => $params) {
                $context->addMethodCall('addFormat', array($formatName, $params));
            }

            $manager->addMethodCall('addContext', array($name, $context));
        }
    }

    public function getAlias()
    {
        return 'kunstmaan_k_media';
    }
}