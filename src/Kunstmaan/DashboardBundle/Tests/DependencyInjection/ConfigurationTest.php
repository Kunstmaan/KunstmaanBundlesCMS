<?php

namespace Kunstmaan\DashboardBundle\Tests\DependencyInjection;

use Kunstmaan\DashboardBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): \Symfony\Component\Config\Definition\ConfigurationInterface
    {
        return new Configuration();
    }

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [
            'google_analytics' => [
                'api' => [
                    'client_id' => null,
                    'client_secret' => null,
                    'dev_key' => null,
                    'app_name' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
