<?php

namespace Kunstmaan\AdminListBundle\Tests\DependencyInjection;

use Kunstmaan\AdminListBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [
            'lock' => [
                'enabled' => true,
                'check_interval' => 15,
                'threshold' => 35,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
