<?php

namespace Kunstmaan\NodeSearchBundle\Tests\unit\DependencyInjection\Compiler;

use Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler\NodeSearcherCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NodeSearcherCompilerPassTest extends TestCase
{
    public function testTaggerSearchers()
    {
        $container = new ContainerBuilder();

        $container->register('kunstmaan_node_search.search.service', 'Kunstmaan\NodeSearchBundle\Services\SearchService')
            ->setArguments([new Reference('service_container'), new Reference('request_stack'), 10, []])
        ;

        $container->register('custom_searcher_1', 'stdClass')->addTag('kunstmaan_node_search.node_searcher');
        $container->register('custom_searcher_2', 'stdClass');

        $pass = new NodeSearcherCompilerPass();
        $pass->process($container);
        $arguments = $container->getDefinition('kunstmaan_node_search.search.service')->getArguments();

        $expected = [
            'custom_searcher_1',
        ];

        $this->assertCount(4, $arguments);
        $this->assertEquals($expected, array_keys($arguments[3]));
    }
}
