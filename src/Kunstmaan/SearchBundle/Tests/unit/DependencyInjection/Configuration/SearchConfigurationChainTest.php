<?php

namespace Kunstmaan\SearchBundle\Tests\DependencyInjection\Configuration;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use PHPUnit\Framework\TestCase;

class SearchConfigurationChainTest extends TestCase
{
    public function testAddAndGetConfiguration()
    {
        $configuration = $this->createMock('Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface');

        $chain = new SearchConfigurationChain();
        $chain->addConfiguration($configuration, 'configuration');

        $this->assertEquals($configuration, $chain->getConfiguration('configuration'));
        $this->assertNotEmpty($chain->getConfigurations());
        $this->assertNull($chain->getConfiguration('youwontfindthis'));
    }
}
