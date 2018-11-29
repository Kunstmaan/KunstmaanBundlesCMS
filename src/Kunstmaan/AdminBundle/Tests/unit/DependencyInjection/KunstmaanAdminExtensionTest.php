<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanAdminExtensionTest
 */
class KunstmaanAdminExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanAdminExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('kernel.logs_dir', '/somewhere/over/the/rainbow');
        $this->container->setParameter('kernel.environment', 'staging');
        $this->load([
            'dashboard_route' => true,
            'admin_password' => 'omgchangethis',
            'menu_items' => [
                [
                    'route' => 'route66',
                    'label' => 'Route 66',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('version_checker.url', 'https://bundles.kunstmaan.be/version-check');
        $this->assertContainerBuilderHasParameter('version_checker.timeframe', (60 * 60 * 24));
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.admin_locales');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_admin_locale');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.ip_check');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.user_agent_check');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.enabled');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.client_id');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.client_secret');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.hosted_domains');
    }
}
