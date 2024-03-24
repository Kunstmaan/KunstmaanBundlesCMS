<?php

namespace Kunstmaan\VotingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanVotingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['actions'] as $key => $value) {
            if ($value['max_number_by_ip']) {
                $definition = new Definition('Kunstmaan\VotingBundle\EventListener\Security\MaxNumberByIpEventListener');
                $definition->addArgument(new Reference('kunstmaan_voting.services.repository_resolver'));
                $definition->addArgument($value['max_number_by_ip']);
                $definition->addTag('kernel.event_listener', [
                    'event' => 'kunstmaan_voting.' . lcfirst(ContainerBuilder::camelize($key)),
                    'method' => 'onVote',
                    'priority' => 100,
                ]);

                $container->setDefinition('kunstmaan_voting.security.' . $key . '.max_number_by_ip_event_listener', $definition);
            }
        }

        $possibleActions = ['up_vote', 'down_vote', 'facebook_like', 'facebook_send', 'linkedin_share'];

        $votingDefaultValue = $config['voting_default_value'];

        // If the user overwrites the voting_default_value in paramters file, we use this one
        if ($container->hasParameter('voting_default_value')) {
            $votingDefaultValue = $container->getParameter('voting_default_value');
        }

        // When no values are defined, initialize with defaults
        foreach ($possibleActions as $action) {
            if (!isset($config['actions'][$action]) || !\is_array($config['actions'][$action])) {
                $config['actions'][$action]['default_value'] = ($action == 'down_vote' ? -1 * $votingDefaultValue : $votingDefaultValue);
            }
        }

        $container->setParameter('kuma_voting.actions', $config['actions']);
    }
}
