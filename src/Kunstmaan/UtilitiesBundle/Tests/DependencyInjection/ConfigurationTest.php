<?php

namespace Kunstmaan\UtilitiesBundle\Tests\DependencyInjection;

use Kunstmaan\UtilitiesBundle\DependencyInjection\Configuration;
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
        $array = ['cipher' => ['secret' => '%kernel.secret%']];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
