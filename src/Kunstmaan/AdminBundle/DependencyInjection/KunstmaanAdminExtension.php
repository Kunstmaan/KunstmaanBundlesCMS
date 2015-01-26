<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use InvalidArgumentException;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->setParameter('security.acl.permission.map.class', 'Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap');
        $container->setParameter('version_checker.url', 'http://bundles.kunstmaan.be/version-check');
        $container->setParameter('version_checker.timeframe', 60*60*24);
        $container->setParameter('version_checker.enabled', true);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (array_key_exists('dashboard_route', $config)) {
            $container->setParameter('kunstmaan_admin.dashboard_route', $config['dashboard_route']);
        }
        $container->setParameter('kunstmaan_admin.admin_locales', $config['admin_locales']);
        $container->setParameter('kunstmaan_admin.default_admin_locale', $config['default_admin_locale']);

        $container->setParameter('kunstmaan_admin.session_security.ip_check', $config['session_security']['ip_check']);
        $container->setParameter('kunstmaan_admin.session_security.user_agent_check', $config['session_security']['user_agent_check']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $knpMenuConfig['twig']              = true; // set to false to disable the Twig extension and the TwigRenderer
        $knpMenuConfig['templating']        = false; // if true, enables the helper for PHP templates
        $knpMenuConfig['default_renderer']  = 'twig'; // The renderer to use, list is also available by default
        $container->prependExtensionConfig('knp_menu', $knpMenuConfig);

        $fosUserConfig['db_driver']                     = 'orm'; // other valid values are 'mongodb', 'couchdb'
        $fosUserConfig['firewall_name']                 = 'main';
        $fosUserConfig['user_class']                    = 'Kunstmaan\AdminBundle\Entity\User';
        $fosUserConfig['group']['group_class']          = 'Kunstmaan\AdminBundle\Entity\Group';
        $fosUserConfig['resetting']['token_ttl']        = 86400;
        // Use this node only if you don't want the global email address for the resetting email
        $fosUserConfig['resetting']['email']['from_email']['address']        = 'admin@kunstmaan.be';
        $fosUserConfig['resetting']['email']['from_email']['sender_name']    = 'admin';
        $fosUserConfig['resetting']['email']['template']    = 'FOSUserBundle:Resetting:email.txt.twig';
        $fosUserConfig['resetting']['form']['type']                 = 'fos_user_resetting';
        $fosUserConfig['resetting']['form']['name']                 = 'fos_user_resetting_form';
        $fosUserConfig['resetting']['form']['validation_groups']    = array('ResetPassword');
        $container->prependExtensionConfig('fos_user', $fosUserConfig);

        $monologConfig['handlers']['main']['type']  = 'rotating_file';
        $monologConfig['handlers']['main']['path']  = sprintf('%s/%s', $container->getParameter('kernel.logs_dir'), $container->getParameter('kernel.environment'));
        $monologConfig['handlers']['main']['level'] = 'debug';
        $container->prependExtensionConfig('monolog', $monologConfig);

        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return 'http://bundles.kunstmaan.be/schema/dic/admin';
    }

    /**
     * {@inheritDoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }
}
