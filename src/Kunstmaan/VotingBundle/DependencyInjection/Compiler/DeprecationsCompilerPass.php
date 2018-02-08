<?php

namespace Kunstmaan\VotingBundle\DependencyInjection\Compiler;

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
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\VotingBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_voting.listener.vote', AbstractVoteListener::class],
                ['kunstmaan_voting.helper.vote', AbstractVotingHelper::class],
                ['kunstmaan_voting.upvote', UpVoteEventListener::class],
                ['kunstmaan_voting.helper.upvote', UpVoteHelper::class],
                ['kunstmaan_voting.downvote', DownVoteEventListener::class],
                ['kunstmaan_voting.helper.downvote', DownVoteHelper::class],
                ['kunstmaan_voting.facebooklike', FacebookLikeEventListener::class],
                ['kunstmaan_voting.helper.facebook.like', FacebookLikeHelper::class],
                ['kunstmaan_voting.facebooksend', FacebookSendEventListener::class],
                ['kunstmaan_voting.helper.facebook.send', FacebookSendHelper::class],
                ['kunstmaan_voting.linkedinshare', LinkedInShareEventListener::class],
                ['kunstmaan_voting.helper.linkedin.share', LinkedInShareHelper::class],
                ['kunstmaan_voting.services.repository_resolver', RepositoryResolver::class],
            ]
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations)
    {
        foreach ($deprecations as $deprecation) {
            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            $definition->setClass($deprecation[1]);
            $definition->setDeprecated(
                true,
                'Passing a "%service_id%" instance is deprecated since KunstmaanVotingBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
            );
            $container->setDefinition($deprecation[0], $definition);
        }
    }
}
