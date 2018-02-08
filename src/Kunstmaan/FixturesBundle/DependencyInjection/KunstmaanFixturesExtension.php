<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection;

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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanFixturesExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_fixtures.builder.builder' => new Alias(BuildingSupervisor::class),
                'kunstmaan_fixtures.builder.page' => new Alias(PageBuilder::class),
                'kunstmaan_fixtures.builder.pagepart' => new Alias(PagePartBuilder::class),
                'kunstmaan_fixtures.builder.media' => new Alias(MediaBuilder::class),
                'kunstmaan_fixtures.builder.menuitem' => new Alias(MenuItemBuilder::class),
                'kunstmaan_fixtures.parser.parser' => new Alias(Parser::class),
                'kunstmaan_fixtures.parser.property.method' => new Alias(Method::class),
                'kunstmaan_fixtures.parser.property.reference' => new Alias(Reference::class),
                'kunstmaan_fixtures.parser.spec.range' => new Alias(Range::class),
                'kunstmaan_fixtures.parser.spec.listed' => new Alias(Listed::class),
                'kunstmaan_fixtures.populator.populator' => new Alias(Populator::class),
                'kunstmaan_fixtures.populator.method.property' => new Alias(Property::class),
                'kunstmaan_fixtures.populator.method.setter' => new Alias(Setter::class),
                'kunstmaan_fixtures.populator.method.array' => new Alias(ArrayAdd::class),
                'kunstmaan_fixtures.provider.spec' => new Alias(Spec::class),
                'kunstmaan_fixtures.provider.nodetranslation' => new Alias(NodeTranslation::class),
                'kunstmaan_fixtures.provider.node' => new Alias(Node::class),
            ]
        );
        // === END ALIASES ====
    }
}
