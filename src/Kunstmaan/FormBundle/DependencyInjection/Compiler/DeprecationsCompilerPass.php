<?php

namespace Kunstmaan\FormBundle\DependencyInjection\Compiler;

use Kunstmaan\FormBundle\EventListener\ConfigureActionsMenuListener;
use Kunstmaan\FormBundle\Helper\FormHandler;
use Kunstmaan\FormBundle\Helper\FormMailer;
use Kunstmaan\FormBundle\Helper\Menu\FormSubmissionsMenuAdaptor;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\FormBundle\DependencyInjection\Compiler
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
                ['kunstmaan_form.menu.adaptor.forms', FormSubmissionsMenuAdaptor::class],
                ['kunstmaan_form.form_mailer', FormMailer::class],
                ['kunstmaan_form.form_handler', FormHandler::class],
                ['kunstmaan_form.configure_sub_actions_menu_listener', ConfigureActionsMenuListener::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_form.form_mailer.class', FormMailer::class],
                ['kunstmaan_form.form_handler.class', FormHandler::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanFormBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanFormBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}
