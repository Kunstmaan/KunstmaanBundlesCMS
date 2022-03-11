<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\Service\UserManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InjectPasswordHasherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('security.password_hasher_factory')) {
            return;
        }

        $container->getDefinition(UserManager::class)->setArgument('$hasherFactory', $container->getDefinition('security.password_hasher_factory'));
    }
}
