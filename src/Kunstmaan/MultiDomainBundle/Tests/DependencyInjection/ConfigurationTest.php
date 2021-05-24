<?php

namespace Kunstmaan\MultiDomainBundle\Tests\DependencyInjection;

use Kunstmaan\MultiDomainBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testInvalidConfiguration()
    {
        $this->assertConfigurationIsInvalid([[]], '/The child (node|config) "hosts" (at path|under) "kunstmaan_multi_domain" must be configured./', true);
    }

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [
            'hosts' => [
                'host_one' => [
                    'host' => 'cia.gov',
                    'protocol' => 'https',
                    'aliases' => ['cia.com'],
                    'type' => 'single_lang',
                    'root' => 'homepage',
                    'default_locale' => 'nl',
                    'locales' => [
                        [
                            'uri_locale' => '/nl',
                            'locale' => 'nl',
                            'extra' => 'huh?',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
