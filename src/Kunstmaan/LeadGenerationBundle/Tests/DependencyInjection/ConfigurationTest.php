<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\DependencyInjection;

use Kunstmaan\LeadGenerationBundle\DependencyInjection\Configuration;
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
            'popup_types' => ['one element'],
            'debug' => true,
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }

    public function testConfigDoesntGenerateAsExpected()
    {
        $array = [];

        $this->assertPartialConfigurationIsInvalid([$array], 'popup_types');
    }
}
