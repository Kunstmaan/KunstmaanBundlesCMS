<?php

namespace Kunstmaan\FormBundle\Tests\DependencyInjection;

use Kunstmaan\FormBundle\DependencyInjection\Configuration;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $object = new Configuration();
        $tree = $object->getConfigTreeBuilder();

        $this->assertInstanceOf(TreeBuilder::class, $tree);

        $node = $tree->root('hosts');
        $this->assertInstanceOf(ArrayNodeDefinition::class, $node);
    }
}
