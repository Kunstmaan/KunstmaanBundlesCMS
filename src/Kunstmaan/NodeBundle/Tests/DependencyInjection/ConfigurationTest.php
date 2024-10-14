<?php

namespace Kunstmaan\NodeBundle\Tests\DependencyInjection;

use Kunstmaan\NodeBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): \Symfony\Component\Config\Definition\ConfigurationInterface
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
            'show_duplicate_with_children' => false,
            'enable_export_page_template' => false,
            'lock' => [
                'enabled' => true,
                'check_interval' => 15,
                'threshold' => 35,
            ],
            'enable_permissions' => true,
            'enable_improved_urlchooser' => false,
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
