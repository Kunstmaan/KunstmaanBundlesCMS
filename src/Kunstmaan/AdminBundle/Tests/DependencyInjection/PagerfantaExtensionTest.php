<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use BabDev\PagerfantaBundle\DependencyInjection\BabDevPagerfantaExtension;
use Kunstmaan\AdminBundle\DependencyInjection\PagerfantaExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;

class PagerfantaExtensionTest extends AbstractExtensionTestCase
{
    public function testWhiteOctoberBundleConfigPrependedToBabDevBundle(): void
    {
        $this->container->setParameter(
            'kernel.bundles',
            [
                'BabDevPagerfantaBundle' => BabDevPagerfantaBundle::class,
                'TwigBundle' => TwigBundle::class,
            ]
        );

        $bundleConfig = [
            'default_view' => 'twitter_bootstrap',
            'exceptions_strategy' => [
                'out_of_range_page' => 'custom',
                'not_valid_current_page' => 'to_http_not_found',
            ],
        ];

        // Prepend config now to allow the prepend pass to work
        $this->container->prependExtensionConfig('white_october_pagerfanta', $bundleConfig);

        $this->load($bundleConfig);

        $this->assertSame([$bundleConfig], $this->container->getExtensionConfig('babdev_pagerfanta'));
        $this->assertContainerBuilderHasParameter('white_october_pagerfanta.default_view', $bundleConfig['default_view']);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new PagerfantaExtension(),
            new BabDevPagerfantaExtension(),
        ];
    }
}
