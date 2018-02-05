<?php

namespace Tests\Kunstmaan\MultiDomainBundle\Entity;

use Kunstmaan\MultiDomainBundle\DependencyInjection\Configuration;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $object = new Configuration();
        $builder = $object->getConfigTreeBuilder();
        $this->assertInstanceOf(TreeBuilder::class, $builder);
        $tree = $builder->buildTree();
        $this->assertInstanceOf(ArrayNode::class, $tree);
        $this->assertEquals('kunstmaan_multi_domain', $tree->getName());
    }
}
