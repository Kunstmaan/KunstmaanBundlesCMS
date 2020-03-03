<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class InjectUntrackedTokenStorageCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('security.untracked_token_storage')) {
            return;
        }

        // NEXT_MAJOR: this compilerpass can be removed when we drop symfony <4.4 support + replace service argument in yaml config
        if ($container->has('kunstmaan_admin.logger.processor.user')) {
            $container->getDefinition('kunstmaan_admin.logger.processor.user')->setArgument(0, new Reference('security.untracked_token_storage'));
        }
    }
}
