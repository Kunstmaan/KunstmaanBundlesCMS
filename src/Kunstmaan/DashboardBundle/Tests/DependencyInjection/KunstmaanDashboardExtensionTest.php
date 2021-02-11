<?php

namespace Kunstmaan\DashboardBundle\Tests\DependencyInjection;

use Kunstmaan\DashboardBundle\DependencyInjection\KunstmaanDashboardExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanDashboardExtensionTest extends AbstractExtensionTestCase
{
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

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.widget.googleanalytics.command');
        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.widget.googleanalytics.controller');
        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.googleclient.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.googleclienthelper.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.app_name');
    }

    public function testGoogleAnalyticsApiClientIdWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_id', '');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_dashboard.google_analytics.api.client_id" config while setting the "google.api.client_id" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.client_id" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testGoogleAnalyticsApiClientIdWithParameterSet()
    {
        $this->setParameter('google.api.client_id', 'client_id');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_id', 'client_id');
    }

    public function testGoogleAnalyticsApiClientIdWithParameterAndConfigSet()
    {
        $this->setParameter('google.api.client_id', 'client_id');

        $this->load(['google_analytics' => ['api' => ['client_id' => 'custom_client_id']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_id', 'custom_client_id');
    }

    public function testGoogleAnalyticsApiClientSecretWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_secret', '');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_dashboard.google_analytics.api.client_secret" config while setting the "google.api.client_secret" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.client_secret" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testGoogleAnalyticsApiClientSecretWithParameterSet()
    {
        $this->setParameter('google.api.client_secret', 'client_secret');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_secret', 'client_secret');
    }

    public function testGoogleAnalyticsApiClientSecretWithParameterAndConfigSet()
    {
        $this->setParameter('google.api.client_secret', 'client_secret');

        $this->load(['google_analytics' => ['api' => ['client_secret' => 'custom_client_secret']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.client_secret', 'custom_client_secret');
    }

    public function testGoogleAnalyticsApiDevKeyWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.dev_key', '');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_dashboard.google_analytics.api.dev_key" config while setting the "google.api.dev_key" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.dev_key" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testGoogleAnalyticsApiDevKeyWithParameterSet()
    {
        $this->setParameter('google.api.dev_key', 'dev_key');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.dev_key', 'dev_key');
    }

    public function testGoogleAnalyticsApiDevKeyWithParameterAndConfigSet()
    {
        $this->setParameter('google.api.dev_key', 'dev_key');

        $this->load(['google_analytics' => ['api' => ['dev_key' => 'custom_dev_key']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.dev_key', 'custom_dev_key');
    }

    public function testGoogleAnalyticsApiAppNameWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.app_name', 'Kuma Analytics Dashboard');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_dashboard.google_analytics.api.app_name" config while setting the "google.api.app_name" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.app_name" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testGoogleAnalyticsApiAppNameWithParameterSet()
    {
        $this->setParameter('google.api.app_name', 'app_name');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.app_name', 'app_name');
    }

    public function testGoogleAnalyticsApiAppNameWithParameterAndConfigSet()
    {
        $this->setParameter('google.api.app_name', 'app_name');

        $this->load(['google_analytics' => ['api' => ['app_name' => 'custom_app_name']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.google_analytics.api.app_name', 'custom_app_name');
    }
}
