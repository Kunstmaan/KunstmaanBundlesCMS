<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class DeprecateClassParametersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $expectedValues = [
            'kunstmaan_admin.consoleexception.class' => \Kunstmaan\AdminBundle\EventListener\ConsoleExceptionListener::class,
            'kunstmaan_admin.menubuilder.class' => \Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder::class,
            'kunstmaan_admin.admin_panel.class' => \Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanel::class,
            'kunstmaan_admin.login.listener.class' => \Kunstmaan\AdminBundle\EventListener\LoginListener::class,
            'kunstmaan_admin.admin_locale.listener.class' => \Kunstmaan\AdminBundle\EventListener\AdminLocaleListener::class,
            'kunstmaan_admin.acl.helper.class' => \Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::class,
            'kunstmaan_admin.acl.native.helper.class' => \Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper::class,
            'kunstmaan_admin.security.acl.permission.map.class' => \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap::class,
            'kunstmaan_admin.clone.listener.class' => \Kunstmaan\AdminBundle\EventListener\CloneListener::class,
            'kunstmaan_admin.session_security.class' => \Kunstmaan\AdminBundle\EventListener\SessionSecurityListener::class,
            'kunstmaan_admin.password_resetting.listener.class' => \Kunstmaan\AdminBundle\EventListener\PasswordResettingListener::class,
            'kunstmaan_admin.password_check.listener.class' => \Kunstmaan\AdminBundle\EventListener\PasswordCheckListener::class,
            'kunstmaan_admin.domain_configuration.class' => \Kunstmaan\AdminBundle\Helper\DomainConfiguration::class,
            'kunstmaan_admin.validator.password_restrictions.class' => \Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictionsValidator::class,
            'kunstmaan_admin.adminroute.helper.class' => \Kunstmaan\AdminBundle\Helper\AdminRouteHelper::class,
            'kunstmaan_admin.adminroute.twig.class' => \Kunstmaan\AdminBundle\Twig\AdminRouteHelperTwigExtension::class,
            'kunstmaan_admin.exception.listener.class' => \Kunstmaan\AdminBundle\EventListener\ExceptionSubscriber::class,
            'kunstmaan_admin.toolbar.listener.class' => \Kunstmaan\AdminBundle\EventListener\ToolbarListener::class,
            'kunstmaan_admin.toolbar.collector.bundle.class' => \Kunstmaan\AdminBundle\Toolbar\BundleVersionDataCollector::class,
            'kunstmaan_admin.toolbar.collector.exception.class' => \Kunstmaan\AdminBundle\Toolbar\ExceptionDataCollector::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanAdminBundle 5.2 and will be removed in KunstmaanAdminBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}
