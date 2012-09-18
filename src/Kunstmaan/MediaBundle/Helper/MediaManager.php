<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Entity\MediaContext;
use Kunstmaan\MediaBundle\Helper\Cdn\CdnInterface;
use Kunstmaan\MediaBundle\Helper\Provider\ProviderInterface;
use Gaufrette\Filesystem;

/**
 * MediaManager
 */
class MediaManager
{
    /* @var array */
    protected $contexts = array();

    /* @var ProviderInterface[] */
    protected $providers = array();

    /* @var ProviderInterface */
    protected $defaultProvider;

    /* @var array */
    protected $cdns = array();

    /* @var CdnInterface */
    protected $defaultCdn;

    /* @var array */
    protected $filesystems = array();

    /* @var Filesystem */
    protected $defaultFilesystem;


    /**
     * @param string       $name    The context name
     * @param MediaContext $context Media context
     *
     * @return void
     */
    public function addContext($name, MediaContext $context)
    {
        $this->contexts[$name] = $context;
    }

    /**
     * @param string $name
     *
     * @return MediaContext
     *
     * @throws \InvalidArgumentException when there is no context for this name
     */
    public function getContext($name)
    {
        if (!$this->hasContext($name)) {
            throw new \InvalidArgumentException(sprintf('Context "%s" doesn\'t exist', $name));
        }

        return $this->contexts[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasContext($name)
    {
        return array_key_exists($name, $this->contexts);
    }

    /**
     * @return array of MediaContext
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @param array $cdns
     */
    public function setCdns(array $cdns)
    {
        $this->cdns = $cdns;
    }

    /**
     * @return array
     */
    public function getCdns()
    {
        return $this->cdns;
    }

    /**
     * @param string           $name name
     * @param Cdn\CdnInterface $cdn  cdn
     */
    public function addCdn($name, CdnInterface $cdn)
    {
        $this->cdns[$name] = $cdn;
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getCdn($name)
    {
        if (!$this->hasCdn($name)) {
            throw new \InvalidArgumentException(sprintf('Cdn "%s" doesn\'t exist', $name));
        }

        return $this->cdns[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasCdn($name)
    {
        return array_key_exists($name, $this->cdns);
    }

    /**
     * @param CdnInterface $defaultCdn
     */
    public function setDefaultCdn(CdnInterface $defaultCdn)
    {
        $this->defaultCdn = $defaultCdn;
    }

    /**
     * @return CdnInterface
     */
    public function getDefaultCdn()
    {
        return $this->defaultCdn;
    }

    /**
     * @param ProviderInterface $defaultProvider
     */
    public function setDefaultProvider(ProviderInterface $defaultProvider)
    {
        $this->defaultProvider = $defaultProvider;
    }

    /**
     * @return ProviderInterface
     */
    public function getDefaultProvider()
    {
        return $this->defaultProvider;
    }

    /**
     * @param array $providers
     */
    public function setProviders($providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param string            $name     The name
     * @param ProviderInterface $provider The provider
     */
    public function addProvider($name, ProviderInterface $provider)
    {
        $this->providers[$name] = $provider;
    }

    /**
     * @param string $name
     *
     * @return ProviderInterface
     * @throws \InvalidArgumentException
     */
    public function getProvider($name)
    {
        if (!$this->hasProvider($name)) {
            throw new \InvalidArgumentException(sprintf('Provider "%s" doesn\'t exist', $name));
        }

        return $this->providers[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProvider($name)
    {
        return array_key_exists($name, $this->providers);
    }

    /**
     * @param Filesystem $defaultFilesystem
     */
    public function setDefaultFilesystem(Filesystem $defaultFilesystem)
    {
        $this->defaultFilesystem = $defaultFilesystem;
    }

    /**
     * @return Filesystem
     */
    public function getDefaultFilesystem()
    {
        return $this->defaultFilesystem;
    }

    /**
     * @param array $filesystems
     */
    public function setFilesystems($filesystems)
    {
        $this->filesystems = $filesystems;
    }

    /**
     * @return array
     */
    public function getFilesystems()
    {
        return $this->filesystems;
    }

    /**
     * @param string     $name       The name
     * @param Filesystem $filesystem The filesystem
     */
    public function addFilesystem($name, Filesystem $filesystem)
    {
        $this->filesystems[$name] = $filesystem;
    }

    /**
     * @param string $name
     *
     * @return FileSystem
     * @throws \InvalidArgumentException
     */
    public function getFilesystem($name)
    {
        if (!$this->hasFilesystem($name)) {
            throw new \InvalidArgumentException(sprintf('Filesystem "%s" doesn\'t exist', $name));
        }

        return $this->filesystems[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFilesystem($name)
    {
        return array_key_exists($name, $this->filesystems);
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function prepareMedia(Media $media)
    {
        $context = $this->getContext($media->getContext());
        $context->getProvider()->prepareMedia($media);
    }

    /**
     * @param Media $media The media
     * @param bool  $new   Is new
     */
    public function saveMedia(Media $media, $new = false)
    {
        $context = $this->getContext($media->getContext());
        $context->getProvider()->setFormats($context->getFormats());

        if ($new) {
            $context->getProvider()->saveMedia($media);
        } else {
            $context->getProvider()->updateMedia($media);
        }
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function removeMedia(Media $media)
    {
        $context = $this->getContext($media->getContext());
        $context->getProvider()->setFormats($context->getFormats());
        $context->getProvider()->removeMedia($media);
    }
}