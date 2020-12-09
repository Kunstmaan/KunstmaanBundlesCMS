<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\DependencyInjection;

use Kunstmaan\LeadGenerationBundle\DependencyInjection\Configuration;
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
