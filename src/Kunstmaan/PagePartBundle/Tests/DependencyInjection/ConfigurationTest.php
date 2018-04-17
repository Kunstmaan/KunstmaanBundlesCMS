<?php

namespace Kunstmaan\PagePartBundle\Tests\DependencyInjection;

use Kunstmaan\PagePartBundle\DependencyInjection\Configuration;
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
            'extended_pagepart_chooser' => true,
            'pageparts' => [],
            'pagetemplates' => [],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
