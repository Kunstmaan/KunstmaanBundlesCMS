<?php

namespace Kunstmaan\MultiDomainBundle\Tests\DependencyInjection;

use Kunstmaan\MultiDomainBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit_Framework_TestCase;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
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
        $this->assertConfigurationIsInvalid([[]], 'The child node "hosts" at path "kunstmaan_multi_domain" must be configured.');
    }

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [
            'hosts' => [
                [
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
