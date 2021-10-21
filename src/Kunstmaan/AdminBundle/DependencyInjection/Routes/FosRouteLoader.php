<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Routes;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * NEXT_MAJOR: remove class
 *
 * @internal
 */
final class FosRouteLoader
{
    /** @var bool */
    private $newAuthenticationEnabled;

    public function __construct(bool $newAuthenticationEnabled = false)
    {
        $this->newAuthenticationEnabled = $newAuthenticationEnabled;
    }

    public function loadRoutes()
    {
        if ($this->newAuthenticationEnabled) {
            return new RouteCollection();
        }

        $fileLocator = new FileLocator([__DIR__ . '/../../Resources/config']);
        $loaderResolver = new LoaderResolver([new XmlFileLoader($fileLocator)]);
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        return $delegatingLoader->load('routing_fos.xml');
    }
}
