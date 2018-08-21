<?php

namespace Kunstmaan\NodeBundle\Tests\DependencyInjection;

use Kunstmaan\NodeBundle\DependencyInjection\Configuration;
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

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'pages' => [],
            'publish_later_stepping' => '15',
            'unpublish_later_stepping' => '15',
            'show_add_homepage' => true,
            'enable_export_page_template' => false,
            'lock' => [
                'enabled' => true,
                'check_interval' => 15,
                'threshold' => 35,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
