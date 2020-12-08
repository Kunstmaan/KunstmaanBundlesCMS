<?php

namespace Kunstmaan\ConfigBundle\DependencyInjection\Compiler;

use Exception;
use InvalidArgumentException;
use Kunstmaan\ConfigBundle\Entity\ConfigurationInterface;
use ReflectionException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @deprecated This class is deprecated in KunstmaanConfigBundle 5.3 and will be removed in KunstmaanConfigBundle 6.0. The entity validation is moved to the bundle configuration instead.
 */
class KunstmaanConfigConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        try {
            $backendConfiguration = $container->getParameter('kunstmaan_config');

            if (empty($backendConfiguration['entities'])) {
                throw new \RuntimeException('You need to provide at least one config entity for this bundle to work.');
            }

            // Check if entity exists.
            foreach ($backendConfiguration['entities'] as $class) {
                try {
                    $container->get('doctrine')->getManagerForClass($class);
                } catch (ReflectionException $e) {
                    throw new InvalidArgumentException(sprintf('Entity "%s" does not exist', $class));
                }

                // Check if entity implements the ConfigurationInterface.
                if (!\in_array(ConfigurationInterface::class, class_implements($class))) {
                    throw new RuntimeException(sprintf('The entity class "%s" needs to implement the ConfigurationInterface', $class));
                }
            }

            return $backendConfiguration;
        } catch (Exception $e) {
            return [];
        }
    }
}
