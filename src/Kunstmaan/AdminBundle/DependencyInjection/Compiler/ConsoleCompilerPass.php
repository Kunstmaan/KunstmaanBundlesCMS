<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\EventListener\ConsoleExceptionListener;
use Kunstmaan\AdminBundle\EventListener\ConsoleExceptionSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConsoleCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // if the old listener is no longer in use
        if (!$container->hasDefinition('kunstmaan_admin.consolelogger.listener')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_admin.consolelogger.listener');

        // if the default setup is in use, the subscriber / listener take care of correctly handling the errors
        if (
            $container->hasParameter('kunstmaan_admin.consoleexception.class') &&
            ConsoleExceptionListener::class === $container->getParameter('kunstmaan_admin.consoleexception.class') &&
            '%kunstmaan_admin.consoleexception.class%' === $definition->getClass()
        ) {
            return;
        }

        // if the listener has been overwritten in any way, a deprecation warning is needed
        @trigger_error(
            sprintf(
                'The "%s" is deprecated and replaced by "%s" since KunstmaanAdminBundle 5.1 and will be removed in KunstmaanAdminBundle 6.0.',
                ConsoleExceptionListener::class,
                ConsoleExceptionSubscriber::class
            ),
            E_USER_DEPRECATED
        );
    }
}
