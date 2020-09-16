<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Routes;

use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class FosRouteLoader implements RouteLoaderInterface
{
    /** @var boolean */
    private $enableFosLogin;

    public function __construct(bool $enableFosLogin)
    {
        $this->enableFosLogin = $enableFosLogin;
    }

    public function loadRoutes()
    {
        $configDirectories = [__DIR__.'/../../Resources/config'];
        $fileLocator = new FileLocator($configDirectories);

        if ($this->enableFosLogin) {
            $loaderResolver = new LoaderResolver([new XmlFileLoader($fileLocator)]);
            $delegatingLoader = new DelegatingLoader($loaderResolver);
            $collection = $delegatingLoader->load('routing_fos.xml');
        } else {
            $collection = new RouteCollection();
        }

        return $collection;
    }
}
