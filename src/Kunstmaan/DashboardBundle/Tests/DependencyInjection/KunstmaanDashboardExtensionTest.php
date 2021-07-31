<?php

namespace Kunstmaan\DashboardBundle\Tests\DependencyInjection;

use Kunstmaan\DashboardBundle\DependencyInjection\KunstmaanDashboardExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanDashboardExtensionTest extends AbstractExtensionTestCase
{
    use ExpectDeprecationTrait;

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanDashboardExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.app_name');
    }

    public function testGoogleAnalyticsApiClientIdWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_id', '');
    }

    public function testGoogleAnalyticsApiClientIdWithParameterAndConfigSet()
    {
        $this->load(['google_analytics' => ['api' => ['client_id' => 'custom_client_id']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_id', 'custom_client_id');
    }

    public function testGoogleAnalyticsApiClientSecretWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_secret', '');
    }

    public function testGoogleAnalyticsApiClientSecretWithParameterAndConfigSet()
    {
        $this->load(['google_analytics' => ['api' => ['client_secret' => 'custom_client_secret']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_secret', 'custom_client_secret');
    }

    public function testGoogleAnalyticsApiDevKeyWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.dev_key', '');
    }

    public function testGoogleAnalyticsApiDevKeyWithParameterAndConfigSet()
    {
        $this->load(['google_analytics' => ['api' => ['dev_key' => 'custom_dev_key']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.dev_key', 'custom_dev_key');
    }

    public function testGoogleAnalyticsApiAppNameWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.app_name', 'Kuma Analytics Dashboard');
    }

    public function testGoogleAnalyticsApiAppNameWithParameterAndConfigSet()
    {
        $this->load(['google_analytics' => ['api' => ['app_name' => 'custom_app_name']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.app_name', 'custom_app_name');
    }
}
