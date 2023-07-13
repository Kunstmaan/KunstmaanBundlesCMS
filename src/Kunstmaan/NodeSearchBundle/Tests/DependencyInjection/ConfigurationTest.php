<?php

namespace Kunstmaan\NodeSearchBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Kunstmaan\NodeSearchBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration(true);
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'enable_update_listener' => true,
            'use_match_query_for_title' => false,
            'mapping' => [],
            'contexts' => [],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
