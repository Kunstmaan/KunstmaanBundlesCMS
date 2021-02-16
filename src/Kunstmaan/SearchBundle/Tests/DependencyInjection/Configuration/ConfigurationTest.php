<?php

namespace Kunstmaan\SearchBundle\Tests\DependencyInjection\Configuration;

use Kunstmaan\SearchBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'connection' => [
                'driver' => 'elastic_search',
                'host' => 'localhost',
                'port' => 9200,
                'username' => null,
                'password' => null,
            ],
            'index_prefix' => null,
            'analyzer_languages' => [],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }

    public function testConfigWithInvalidConnectionDriver()
    {
        $this->expectExceptionMessage('Invalid configuration for path "kunstmaan_search.connection.driver": Invalid search driver "elasticsearch"');
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);
        $array = [
            'connection' => [
                'driver' => 'elasticsearch',
            ],
            'analyzer_languages' => [],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
