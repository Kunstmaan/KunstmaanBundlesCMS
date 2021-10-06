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

    public function testIndexPrefixWithParameterAndConfigSet()
    {
        $this->setParameter('searchindexprefix', 'prefix');

        $this->load(['index_prefix' => 'other_prefix']);

        $this->assertContainerBuilderHasParameter('kunstmaan_search.index_prefix', 'other_prefix');
    }
}
