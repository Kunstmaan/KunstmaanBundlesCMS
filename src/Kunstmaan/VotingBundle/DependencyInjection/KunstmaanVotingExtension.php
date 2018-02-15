<?php

namespace Kunstmaan\VotingBundle\DependencyInjection;

use Kunstmaan\VotingBundle\EventListener\AbstractVoteListener;
use Kunstmaan\VotingBundle\EventListener\Facebook\FacebookLikeEventListener;
use Kunstmaan\VotingBundle\EventListener\Facebook\FacebookSendEventListener;
use Kunstmaan\VotingBundle\EventListener\LinkedIn\LinkedInShareEventListener;
use Kunstmaan\VotingBundle\EventListener\UpDown\DownVoteEventListener;
use Kunstmaan\VotingBundle\EventListener\UpDown\UpVoteEventListener;
use Kunstmaan\VotingBundle\Helper\AbstractVotingHelper;
use Kunstmaan\VotingBundle\Helper\Facebook\FacebookLikeHelper;
use Kunstmaan\VotingBundle\Helper\Facebook\FacebookSendHelper;
use Kunstmaan\VotingBundle\Helper\LinkedIn\LinkedInShareHelper;
use Kunstmaan\VotingBundle\Helper\UpDown\DownVoteHelper;
use Kunstmaan\VotingBundle\Helper\UpDown\UpVoteHelper;
use Kunstmaan\VotingBundle\Services\RepositoryResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        foreach ($config['actions'] as $key => $value) {

            if ($value['max_number_by_ip']) {

                $definition = new Definition('Kunstmaan\VotingBundle\EventListener\Security\MaxNumberByIpEventListener');
                $definition->addArgument(new Reference('kunstmaan_voting.services.repository_resolver'));
                $definition->addArgument($value['max_number_by_ip']);
                $definition->addTag('kernel.event_listener', array(
                    'event' => 'kunstmaan_voting.' . lcfirst(ContainerBuilder::camelize($key)),
                    'method' => 'onVote',
                    'priority' => 100,
                ));

                $container->setDefinition('kunstmaan_voting.security.' . $key . '.max_number_by_ip_event_listener', $definition);
            }

        }

        $possibleActions = array('up_vote', 'down_vote', 'facebook_like', 'facebook_send', 'linkedin_share');

        $votingDefaultValue = $config['voting_default_value'];

        // If the user overwrites the voting_default_value in paramters file, we use this one
        if ($container->hasParameter('voting_default_value')) {
            $votingDefaultValue = $container->getParameter('voting_default_value');
        }

        // When no values are defined, initialize with defaults
        foreach($possibleActions as $action) {
            if (!isset($config['actions'][$action]) || !\is_array($config['actions'][$action])) {
                $config['actions'][$action]['default_value'] = ( $action === 'down_vote' ? -1 * $votingDefaultValue : $votingDefaultValue );
            }
        }

        $container->setParameter('kuma_voting.actions', $config['actions']);

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_voting.listener.vote' => new Alias(AbstractVoteListener::class),
                'kunstmaan_voting.helper.vote' => new Alias(AbstractVotingHelper::class),
                'kunstmaan_voting.upvote' => new Alias(UpVoteEventListener::class),
                'kunstmaan_voting.helper.upvote' => new Alias(UpVoteHelper::class),
                'kunstmaan_voting.downvote' => new Alias(DownVoteEventListener::class),
                'kunstmaan_voting.helper.downvote' => new Alias(DownVoteHelper::class),
                'kunstmaan_voting.facebooklike' => new Alias(FacebookLikeEventListener::class),
                'kunstmaan_voting.helper.facebook.like' => new Alias(FacebookLikeHelper::class),
                'kunstmaan_voting.facebooksend' => new Alias(FacebookSendEventListener::class),
                'kunstmaan_voting.helper.facebook.send' => new Alias(FacebookSendHelper::class),
                'kunstmaan_voting.linkedinshare' => new Alias(LinkedInShareEventListener::class),
                'kunstmaan_voting.helper.linkedin.share' => new Alias(LinkedInShareHelper::class),
                'kunstmaan_voting.services.repository_resolver' => new Alias(RepositoryResolver::class),
            ]
        );
        // === END ALIASES ====
    }
}
