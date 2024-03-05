<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @internal
 */
final class Sf4AppBundle implements BundleInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function boot(): void
    {
        // no-op
    }

    public function shutdown(): void
    {
        // no-op
    }

    /**
     * It is only ever called once when the cache is empty.
     */
    public function build(ContainerBuilder $container): void
    {
        // no-op
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return null;
    }

    public function getName(): string
    {
        return 'App';
    }

    public function getNamespace(): string
    {
        return 'App';
    }

    public function getPath(): string
    {
        return $this->projectDir . '/src';
    }

    public function setContainer(?ContainerInterface $container = null): void
    {
        // no-op
    }
}
