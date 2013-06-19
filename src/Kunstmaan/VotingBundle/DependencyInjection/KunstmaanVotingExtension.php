<?php

namespace Kunstmaan\VotingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanVotingExtension extends Extension
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

        $possibleActions = array('up_vote', 'down_vote', 'facebook_like', 'facebook_send', 'linkedin_share');

        // When no values are defined, initialize with defaults
        foreach($possibleActions as $action) {
            if (!@is_array($config['actions'][$action])) {
                $config['actions'][$action]['default_value'] = ( $action == 'down_vote' ? -1 * $config['voting_default_value'] : $config['voting_default_value'] );
            }
        }

        $container->setParameter('kuma_voting.actions', $config['actions']);
    }
}
