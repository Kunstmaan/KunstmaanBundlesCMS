<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection\Compiler;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MultiDomainCompilerPass
 *
 * @package Kunstmaan\MultiDomainBundle\DependencyInjection\Compiler
 */
class MultiDomainCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /**
         * We override the default slug router here.
         *
         * You can use a custom one by adding a compiler pass that overrides the class of the definition.
         * If we still have the old parameter way, use it but trigger a deprecation.
         */
        if ($container->hasParameter('kunstmaan_multi_domain.router.class')) {
            $defintion = $container->getDefinition(SlugRouter::class);
            $defintion->setClass($container->getParameter('kunstmaan_multi_domain.router.class'));
            @trigger_error(
                sprintf(
                    'Override SlugRouter class in "%s" is deprecated in KunstmaanMultiDomainBundle 5.1 and will be removed in KunstmaanMultiDomainBundle 6.0. Override the actual class of the original service.',
                    __METHOD__
                ),
                E_USER_DEPRECATED
            );
        } else {
            $defintion = $container->getDefinition(SlugRouter::class);
            $defintion->setClass(DomainBasedLocaleRouter::class);
        }

        /**
         * We override the default domain configuration service here.
         *
         * You can use a custom one by adding a compiler pass that overrides the class of the definition.
         * If we still have the old parameter way, use it but trigger a deprecation.
         */
        if ($container->hasParameter('kunstmaan_multi_domain.domain_configuration.class')) {
            $defintion = $container->getDefinition(DomainConfiguration::class);
            $defintion->setClass($container->getParameter('kunstmaan_multi_domain.domain_configuration.class'));
            @trigger_error(
                sprintf(
                    'Override DomainConfiguration class in "%s" is deprecated in KunstmaanMultiDomainBundle 5.1 and will be removed in KunstmaanMultiDomainBundle 6.0. Override the actual class of the original service.',
                    __METHOD__
                ),
                E_USER_DEPRECATED
            );
        } else {
            $defintion = $container->getDefinition(DomainConfiguration::class);
            $defintion->setClass(\Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::class);
        }
    }

}
