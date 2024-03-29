<?php

namespace Kunstmaan\SeoBundle\Tests\DependencyInjection;

use Kunstmaan\SeoBundle\DependencyInjection\Configuration;
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
        $array = ['request_cache' => 'app.cache'];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
