<?php

namespace Kunstmaan\UtilitiesBundle\Tests\DependencyInjection;

use Kunstmaan\UtilitiesBundle\DependencyInjection\Configuration;
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

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
