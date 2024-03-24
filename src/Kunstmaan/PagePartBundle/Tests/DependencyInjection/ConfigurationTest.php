<?php

namespace Kunstmaan\PagePartBundle\Tests\DependencyInjection;

use Kunstmaan\PagePartBundle\DependencyInjection\Configuration;
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
            'extended_pagepart_chooser' => true,
            'pageparts' => [],
            'pagetemplates' => [],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
