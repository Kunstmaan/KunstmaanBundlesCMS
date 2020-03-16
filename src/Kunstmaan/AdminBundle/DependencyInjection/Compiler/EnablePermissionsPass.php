<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EnablePermissionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('kunstmaan_node.permissions.enabled')) {
            $permissionsEnabledParameter = $container->getParameter('kunstmaan_node.permissions.enabled');

            $container->getDefinition('kunstmaan_admin.acl.helper')->setArgument('$permissionsEnabled', $permissionsEnabledParameter);
            $container->getDefinition('kunstmaan_admin.acl.native.helper')->setArgument('$permissionsEnabled', $permissionsEnabledParameter);
            $aclVoterDefinition = $container->getDefinition('kunstmaan_admin.security.acl.voter');

            $aclVoterDefinition->setArgument('$logger', null);
            $aclVoterDefinition->setArgument('$allowIfObjectIdentityUnavailable', true);
            $aclVoterDefinition->setArgument('$permissionsEnabled', $permissionsEnabledParameter);
        }
    }
}
