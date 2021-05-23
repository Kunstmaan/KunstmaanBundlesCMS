<?php

namespace Kunstmaan\UserManagementBundle\Tests\DependencyInjection;

use Kunstmaan\UserManagementBundle\AdminList\UserAdminListConfigurator;
use Kunstmaan\UserManagementBundle\DependencyInjection\KunstmaanUserManagementExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanUserManagementExtensionTest extends AbstractExtensionTestCase
{
    use ExpectDeprecationTrait;

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanUserManagementExtension()];
    }

    /**
     * @group legacy
     */
    public function testAdminlistConfiguratorClassParameterOverride()
    {
        $this->expectDeprecation('Overriding the user adminlist configurator class with the "kunstmaan_user_management.user_admin_list_configurator.class" parameter is deprecated since KunstmaanUserManagementBundle 5.9 and will not be allowed in KunstmaanUserManagementBundle 6.0. Use the "kunstmaan_user_management.user.adminlist_configurator" config option instead.');

        $this->setParameter('kunstmaan_user_management.user_admin_list_configurator.class', 'CustomUserAdminListConfigurator');
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_user_management.user_admin_list_configurator.class', 'CustomUserAdminListConfigurator');
    }

    public function testAdminlistConfiguratorClassParameterWithConfigValue()
    {
        $this->load(['user' => ['adminlist_configurator' => 'CustomUserAdminListConfigurator']]);

        $this->assertContainerBuilderHasParameter('kunstmaan_user_management.user_admin_list_configurator.class', 'CustomUserAdminListConfigurator');
    }

    public function testDefaultAdminlistConfiguratorClassParameter()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_user_management.user_admin_list_configurator.class', UserAdminListConfigurator::class);
    }
}
