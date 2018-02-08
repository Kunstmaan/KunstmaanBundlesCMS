<?php

namespace Kunstmaan\FormBundle\DependencyInjection;

use Kunstmaan\FormBundle\EventListener\ConfigureActionsMenuListener;
use Kunstmaan\FormBundle\Helper\FormHandler;
use Kunstmaan\FormBundle\Helper\FormHandlerInterface;
use Kunstmaan\FormBundle\Helper\FormMailer;
use Kunstmaan\FormBundle\Helper\Menu\FormSubmissionsMenuAdaptor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanFormExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_form.menu.adaptor.forms' => new Alias(FormSubmissionsMenuAdaptor::class),
                'kunstmaan_form.form_mailer' => new Alias(FormMailer::class),
                'kunstmaan_form.form_handler' => new Alias(FormHandler::class),
                FormHandlerInterface::class => new Alias(FormHandler::class),
                'kunstmaan_form.configure_sub_actions_menu_listener' => new Alias(ConfigureActionsMenuListener::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_form.form_mailer.class', FormMailer::class, true],
                ['kunstmaan_form.form_handler.class', FormHandler::class, true],
            ]
        );
        // === END ALIASES ====
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasParameter('form_submission_rootdir')) {
            $container->setParameter(
                'form_submission_rootdir',
                sprintf('%s/../web/uploads/formsubmissions', $container->getParameter('kernel.root_dir'))
            );
        }

        if (!$container->hasParameter('form_submission_webdir')) {
            $container->setParameter('form_submission_webdir', '/uploads/formsubmissions/');
        }

        $twigConfig['globals']['form_submission_webdir'] = $container->getParameter('form_submission_webdir');
        $container->prependExtensionConfig('twig', $twigConfig);
        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $aliases
     */
    private function addParameteredAliases(ContainerBuilder $container, $aliases)
    {
        foreach ($aliases as $alias) {
            // Don't allow service with same name as class.
            if ($container->getParameter($alias[0]) !== $alias[1]) {
                $container->setAlias(
                    $container->getParameter($alias[0]),
                    new Alias($alias[1], $alias[2])
                );
            }
        }
    }
}
