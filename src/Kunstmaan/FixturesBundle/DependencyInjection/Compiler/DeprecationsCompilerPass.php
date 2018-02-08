<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\Builder\BuildingSupervisor;
use Kunstmaan\FixturesBundle\Builder\MediaBuilder;
use Kunstmaan\FixturesBundle\Builder\MenuItemBuilder;
use Kunstmaan\FixturesBundle\Builder\PageBuilder;
use Kunstmaan\FixturesBundle\Builder\PagePartBuilder;
use Kunstmaan\FixturesBundle\Parser\Parser;
use Kunstmaan\FixturesBundle\Parser\Property\Method;
use Kunstmaan\FixturesBundle\Parser\Property\Reference;
use Kunstmaan\FixturesBundle\Parser\Spec\Listed;
use Kunstmaan\FixturesBundle\Parser\Spec\Range;
use Kunstmaan\FixturesBundle\Populator\Methods\ArrayAdd;
use Kunstmaan\FixturesBundle\Populator\Methods\Property;
use Kunstmaan\FixturesBundle\Populator\Methods\Setter;
use Kunstmaan\FixturesBundle\Populator\Populator;
use Kunstmaan\FixturesBundle\Provider\Node;
use Kunstmaan\FixturesBundle\Provider\NodeTranslation;
use Kunstmaan\FixturesBundle\Provider\Spec;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\FixturesBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_fixtures.builder.builder', BuildingSupervisor::class],
                ['kunstmaan_fixtures.builder.page', PageBuilder::class],
                ['kunstmaan_fixtures.builder.pagepart', PagePartBuilder::class],
                ['kunstmaan_fixtures.builder.media', MediaBuilder::class],
                ['kunstmaan_fixtures.builder.menuitem', MenuItemBuilder::class],
                ['kunstmaan_fixtures.parser.parser', Parser::class],
                ['kunstmaan_fixtures.parser.property.method', Method::class],
                ['kunstmaan_fixtures.parser.property.reference', Reference::class],
                ['kunstmaan_fixtures.parser.spec.range', Range::class],
                ['kunstmaan_fixtures.parser.spec.listed', Listed::class],
                ['kunstmaan_fixtures.populator.populator', Populator::class],
                ['kunstmaan_fixtures.populator.method.property', Property::class],
                ['kunstmaan_fixtures.populator.method.setter', Setter::class],
                ['kunstmaan_fixtures.populator.method.array', ArrayAdd::class],
                ['kunstmaan_fixtures.provider.spec', Spec::class],
                ['kunstmaan_fixtures.provider.nodetranslation', NodeTranslation::class],
                ['kunstmaan_fixtures.provider.node', Node::class],
            ]
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }
            $definition->setClass($deprecation[1]);
            $definition->setDeprecated(
                true,
                'Passing a "%service_id%" instance is deprecated since KunstmaanFixturesBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
            );
            $container->setDefinition($deprecation[0], $definition);
        }
    }
}
