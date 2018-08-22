<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection\CompilerPass;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MultidomainConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('kunstmaan_multi_domain.domain_configuration.class') && $container->getParameter('kunstmaan_multi_domain.domain_configuration.class') !== DomainConfiguration::class) {
            @trigger_error(
                'Overriding the domain configuration class by setting the "kunstmaan_multi_domain.domain_configuration.class" parameter is deprecated since KunstmaanMultiDomainBundle 5.1 and will be removed in KunstmaanMultiDomainBundle 6.0. Register your custom configuration class as a service and override the "kunstmaan_admin.domain_configuration" service alias.',
                E_USER_DEPRECATED
            );

            // Inject the container back, to keep BC, if the user override the domain configuration with the "kunstmaan_multi_domain.domain_configuration.class" parameter.
            if ($container->hasDefinition('kunstmaan_multi_domain.domain_configuration')) {
                $container->getDefinition('kunstmaan_multi_domain.domain_configuration')->setArguments([new Reference('service_container')]);
            }
        }
    }
}
