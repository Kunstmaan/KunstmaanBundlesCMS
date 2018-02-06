<?php

namespace Tests\Kunstmaan\AdminBundle\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class KunstmaanAdminExtensionTest
 */
class KunstmaanAdminExtensionTest extends AbstractExtensionTestCase
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
        $this->load();

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
