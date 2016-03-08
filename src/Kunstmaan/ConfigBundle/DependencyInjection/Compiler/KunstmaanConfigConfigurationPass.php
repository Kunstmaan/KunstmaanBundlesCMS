<?php

namespace Kunstmaan\ConfigBundle\DependencyInjection\Compiler;

use Kunstmaan\ConfigBundle\Entity\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KunstmaanConfigConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $backendConfiguration = $container->getParameter('kunstmaan_config');

        if (empty($backendConfiguration['entities'])) {
            throw new \RuntimeException('You need to provide at least one config entity for this bundle to work.');
        }

        // Check if entity exists.
        foreach ($backendConfiguration['entities'] as $class) {
            try {
                $container->get('doctrine')->getManagerForClass($class);
            } catch (\ReflectionException $e) {
                throw new \InvalidArgumentException(sprintf('Entity "%s" does not exist', $class));
            }

            // Check if entity implements the ConfigurationInterface.
            if (!in_array(ConfigurationInterface::class, class_implements($class))) {
                throw new \RuntimeException(sprintf('The entity class "%s" needs to implement the ConfigurationInterface', $class));
            }
        }

        return $backendConfiguration;
    }
}
