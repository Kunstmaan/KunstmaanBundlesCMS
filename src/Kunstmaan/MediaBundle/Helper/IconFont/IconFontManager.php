<?php

namespace Kunstmaan\MediaBundle\Helper\IconFont;

/**
 * IconFontManager
 */
class IconFontManager
{
    /**
     * @var IconFontLoaderInterface[]
     */
    protected $loaders = array();

    /**
     * @var IconFontLoaderInterface
     */
    protected $defaultLoader = null;

    /**
     * @param IconFontLoaderInterface $loader
     * @param string                  $serviceId
     */
    public function addLoader(IconFontLoaderInterface $loader, $serviceId)
    {
        $this->loaders[$serviceId] = $loader;
    }

    /**
     * @param IconFontLoaderInterface $loader
     */
    public function setDefaultLoader(IconFontLoaderInterface $loader)
    {
        $this->defaultLoader = $loader;
    }

    /**
     * @param string $serviceId
     *
     * @return IconFontLoaderInterface
     */
    public function getLoader($serviceId)
    {
        return $this->loaders[$serviceId];
    }

    /**
     * @return IconFontLoaderInterface[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @return IconFontLoaderInterface|null
     */
    public function getDefaultLoader()
    {
        return $this->defaultLoader;
    }
}
