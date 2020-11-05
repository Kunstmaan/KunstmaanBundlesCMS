<?php

namespace Kunstmaan\FormBundle\Tests\DependencyInjection;

use Kunstmaan\FormBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends TestCase
{
    public function testConfiguration()
    {
        $object = new Configuration();
        $tree = $object->getConfigTreeBuilder();

        $this->assertInstanceOf(TreeBuilder::class, $tree);

        if (method_exists($tree, 'getRootNode')) {
            $node = $tree->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $node = $tree->root('kunstmaan_form');
        }
        $this->assertInstanceOf(ArrayNodeDefinition::class, $node);
    }
}
