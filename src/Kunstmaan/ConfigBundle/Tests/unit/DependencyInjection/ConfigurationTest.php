<?php

namespace Kunstmaan\ConfigBundle\Tests\DependencyInjection;

use Kunstmaan\ConfigBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationTest
 */
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

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'entities' => [],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }

    public function testConfigDoesntGenerateAsExpected()
    {
        $this->assertPartialConfigurationIsInvalid([['fail']], 'entities');
    }
}
