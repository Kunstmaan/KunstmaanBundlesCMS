<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\DependencyInjection\Container;

/**
 * Generates a bundle.
 */
class BundleGenerator extends Generator
{
    /**
     * @param string $namespace The namespace
     * @param string $bundle    The bundle name
     * @param string $dir       The directory
     *
     * @throws \RuntimeException
     */
    public function generate($namespace, $bundle, $dir)
    {
        $dir .= '/' . strtr($namespace, '\\', '/');
        if (file_exists($dir)) {
            throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
        }

        $basename = substr($bundle, 0, -6);
        $parameters = array(
            'namespace'       => $namespace,
            'bundle'          => $bundle,
            'bundle_basename' => $basename,
            'extension_alias' => Container::underscore($basename),
        );

        $this->renderFile('/bundle/Bundle.php', $dir . '/' . $bundle . '.php', $parameters);
        $this->renderFile('/bundle/Extension.php', $dir . '/DependencyInjection/' . $basename . 'Extension.php', $parameters);
        $this->renderFile('/bundle/Configuration.php', $dir . '/DependencyInjection/Configuration.php', $parameters);
        $this->renderFile('/bundle/DefaultController.php', $dir . '/Controller/DefaultController.php', $parameters);
        $this->renderFile('/bundle/index.html.twig', $dir . '/Resources/views/Default/index.html.twig', $parameters);

        $this->renderFile('/bundle/services.yml', $dir . '/Resources/config/services.yml', $parameters);
        $this->renderFile('/bundle/routing.yml', $dir . '/Resources/config/routing.yml', $parameters);
    }
}
