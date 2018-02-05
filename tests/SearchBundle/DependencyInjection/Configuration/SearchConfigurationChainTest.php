<?php


namespace Tests\Kunstmaan\SearchBundle\DependencyInjection\Configuration;


use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;

class SearchConfigurationChainTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAndGetConfiguration()
    {
        $configuration = $this->createMock('Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface');

        $chain = new SearchConfigurationChain();
        $chain->addConfiguration($configuration, 'configuration');

        $this->assertEquals($configuration, $chain->getConfiguration('configuration'));
    }
}
