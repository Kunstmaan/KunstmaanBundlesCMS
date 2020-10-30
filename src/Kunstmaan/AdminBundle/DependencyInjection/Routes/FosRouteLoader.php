<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Routes;

use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * NEXT_MAJOR: remove class
 *
 * @internal
 */
final class FosRouteLoader implements RouteLoaderInterface
{
    /** @var bool */
    private $enableCustomLogin;

    public function __construct(bool $enableCustomLogin = false)
    {
        $this->enableCustomLogin = $enableCustomLogin;
    }

    public function loadRoutes()
    {
        if ($this->enableCustomLogin) {
            return new RouteCollection();
        }

        $fileLocator = new FileLocator([__DIR__.'/../../Resources/config']);
        $loaderResolver = new LoaderResolver([new XmlFileLoader($fileLocator)]);
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        return $delegatingLoader->load('routing_fos.xml');
    }
}
