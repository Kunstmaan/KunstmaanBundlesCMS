<?php

namespace Kunstmaan\NodeSearchBundle\Tests\DependencyInjection;

use Kunstmaan\NodeSearchBundle\DependencyInjection\Configuration;
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
