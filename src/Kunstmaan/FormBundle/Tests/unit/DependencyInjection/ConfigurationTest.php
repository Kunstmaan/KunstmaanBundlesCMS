<?php

namespace Kunstmaan\FormBundle\Tests\DependencyInjection;

use Kunstmaan\FormBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
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
