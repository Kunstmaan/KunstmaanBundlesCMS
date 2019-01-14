<?php

namespace Kunstmaan\PagePartBundle\Tests\unit\PagePartConfigurationReader;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class LocatingKernelStub implements KernelInterface
{
    public function locateResource($name, $dir = null, $first = true)
    {
        list(, $path) = explode('/', $name, 2);

        return __DIR__ . DIRECTORY_SEPARATOR . $path;
    }

    public function serialize()
    {
    }

    public function unserialize($serialized)
    {
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
    }

    public function registerBundles()
    {
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

    public function boot()
    {
    }

    public function shutdown()
    {
    }

    public function getBundles()
    {
    }

    public function isClassInActiveBundle($class)
    {
    }

    public function getBundle($name, $first = true)
    {
    }

    public function getName()
    {
    }

    public function getEnvironment()
    {
    }

    public function isDebug()
    {
    }

    public function getRootDir()
    {
    }

    public function getContainer()
    {
    }

    public function getStartTime()
    {
    }

    public function getCacheDir()
    {
    }

    public function getLogDir()
    {
    }

    public function getCharset()
    {
    }
}
