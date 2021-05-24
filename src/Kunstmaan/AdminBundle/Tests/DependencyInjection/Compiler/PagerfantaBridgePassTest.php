<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\DependencyInjection\Compiler\PagerfantaBridgePass;
use Kunstmaan\AdminBundle\DependencyInjection\PagerfantaExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Pagerfanta\View\ViewFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PagerfantaBridgePassTest extends AbstractCompilerPassTestCase
{
    public function testWhiteOctoberAliasedServicesAndParametersAreCreated(): void
    {
        $this->registerService('pagerfanta.twig_extension', PagerfantaExtension::class);
        $this->registerService('pagerfanta.view_factory', ViewFactory::class);

        $this->setParameter('white_october_pagerfanta.view_factory.class', 'My\ViewFactory');

        $this->compile();

        $this->assertContainerBuilderHasAlias('twig.extension.pagerfanta', 'pagerfanta.twig_extension');
        $this->assertContainerBuilderHasAlias('white_october_pagerfanta.view_factory', 'pagerfanta.view_factory');
        $this->assertContainerBuilderHasService('pagerfanta.view_factory', 'My\ViewFactory');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PagerfantaBridgePass());
    }
}
