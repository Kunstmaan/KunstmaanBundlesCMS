<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\TranslationsBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class NelmioDefinitionsCompilerPass
 */
class NelmioDefinitionsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('nelmio_api_doc.describers.config');
        $config = $definition->getArgument(0);

        $locator = new FileLocator(__DIR__.'/../../Resources/config/');
        $bundleConfig = Yaml::parseFile($locator->locate('swagger_conf.yml'));

        $config['definitions'] = \array_merge(
            isset($config['definitions']) ? $config['definitions'] : [],
            $bundleConfig['nelmio_api_doc']['documentation']['definitions']
        );

        $definition->replaceArgument(0, $config);
    }
}
