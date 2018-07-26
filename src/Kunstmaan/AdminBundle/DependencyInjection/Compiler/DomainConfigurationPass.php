<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DomainConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('kunstmaan_admin.domain_configuration.class') && $container->getParameter('kunstmaan_admin.domain_configuration.class') !== DomainConfiguration::class) {
            @trigger_error(
                'Overriding the domain configuration class by setting the "kunstmaan_admin.domain_configuration.class" parameter is deprecated since KunstmaanAdminDomainBundle 5.1 and will be removed in KunstmaanAdminDomainBundle 6.0. Register your custom configuration class as a service and override the "kunstmaan_admin.domain_configuration" service alias.',
                E_USER_DEPRECATED
            );

            // Inject the container back, to keep BC, if the user override the domain configuration with the "kunstmaan_admin.domain_configuration.class" parameter.
            if ($container->hasDefinition('kunstmaan_admin.domain_configuration')) {
                $container->getDefinition('kunstmaan_admin.domain_configuration')->setArguments([new Reference('service_container')]);
            }
        }
    }
}
