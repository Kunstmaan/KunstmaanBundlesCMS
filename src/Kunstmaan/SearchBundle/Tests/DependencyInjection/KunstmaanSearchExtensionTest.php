<?php

namespace Kunstmaan\SearchBundle\Tests\DependencyInjection;

use Kunstmaan\SearchBundle\DependencyInjection\KunstmaanSearchExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class KunstmaanSearchExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanSearchExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load([
            'connection' => [
                'driver' => 'elastic_search',
            ],
        ]);

        $this->assertContainerBuilderHasParameter('analyzer_languages');

        $analyzers = $this->container->getParameter('analyzer_languages');

        $this->assertIsArray($analyzers);
        $this->assertArrayHasKey('ar', $analyzers);
        $this->assertEquals('arabic', $analyzers['ar']['analyzer']);
    }

    public function testConnectionHostWithnoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.hostname', 'localhost');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_search.connection.host" config while setting the "kunstmaan_search.hostname" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.hostname" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testConnectionHostWithParameterSet()
    {
        $this->setParameter('kunstmaan_search.hostname', '127.0.0.1');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.hostname', '127.0.0.1');
    }

    public function testConnectionHostWithParameterAndConfigSet()
    {
        $this->setParameter('kunstmaan_search.hostname', '127.0.0.1');

        $this->load(['connection' => ['driver' => 'elastic_search', 'host' => 'localhost']]);

        $this->assertContainerBuilderHasParameter('kunstmaan_search.hostname', 'localhost');
    }

    public function testConnectionPortWithnoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.port', 9200);
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_search.connection.port" config while setting the "kunstmaan_search.port" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.port" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testConnectionPortWithParameterSet()
    {
        $this->setParameter('kunstmaan_search.port', 9300);

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.port', 9300);
    }

    public function testConnectionPortWithParameterAndConfigSet()
    {
        $this->setParameter('kunstmaan_search.port', 9300);

        $this->load(['connection' => ['driver' => 'elastic_search', 'port' => 9205]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_search.port', 9205);
    }

    public function testConnectionUsernameWithnoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.username', null);
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_search.connection.username" config while setting the "kunstmaan_search.username" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.username" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testConnectionUsernameWithParameterSet()
    {
        $this->setParameter('kunstmaan_search.username', 'user');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.username', 'user');
    }

    public function testConnectionUsernameWithParameterAndConfigSet()
    {
        $this->setParameter('kunstmaan_search.username', 'user');

        $this->load(['connection' => ['driver' => 'elastic_search', 'username' => 'config_user']]);

        $this->assertContainerBuilderHasParameter('kunstmaan_search.username', 'config_user');
    }

    public function testConnectionPasswordWithnoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.password', null);
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_search.connection.password" config while setting the "kunstmaan_search.password" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.password" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testConnectionPasswordWithParameterSet()
    {
        $this->setParameter('kunstmaan_search.password', 'password');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.password', 'password');
    }

    public function testConnectionPasswordWithParameterAndConfigSet()
    {
        $this->setParameter('kunstmaan_search.password', 'password');

        $this->load(['connection' => ['driver' => 'elastic_search', 'password' => 'other_password']]);

        $this->assertContainerBuilderHasParameter('kunstmaan_search.password', 'other_password');
    }

    public function testIndexPrefixWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.index_prefix', null);
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_search.index_prefix" config while setting the "searchindexprefix" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "searchindexprefix" parameter in KunstmaanDashboardBundle 6.0.
     */
    public function testIndexPrefixWithParameterSet()
    {
        $this->setParameter('searchindexprefix', 'prefix');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_search.index_prefix', 'prefix');
    }

    public function testIndexPrefixWithParameterAndConfigSet()
    {
        $this->setParameter('searchindexprefix', 'prefix');

        $this->load(['index_prefix' => 'other_prefix']);

        $this->assertContainerBuilderHasParameter('kunstmaan_search.index_prefix', 'other_prefix');
    }
}
