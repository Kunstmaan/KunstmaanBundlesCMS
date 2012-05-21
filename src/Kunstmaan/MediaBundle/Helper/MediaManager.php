<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Entity\MediaContext;
use Kunstmaan\MediaBundle\Helper\Cdn\CdnInterface;
use Kunstmaan\MediaBundle\Helper\Provider\ProviderInterface;
use Gaufrette\Filesystem;

class MediaManager
{
    /* @var array */
    protected $contexts = array();

    /* @var array */
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
     * @param string       $name
     * @param MediaContext $context
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
     * @return \Ano\Bundle\MediaBundle\Model\MediaContext
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
     * @return boolean
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
     * @param $name
     * @param Cdn\CdnInterface $cdn
     */
    public function addCdn($name, CdnInterface $cdn)
    {
        $this->cdns[$name] = $cdn;
    }

    /**
     * @param $name
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
     * @param $name
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
     * @param ProviderInterface
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
     * @param $name
     * @param Provider\ProviderInterface $provider
     */
    public function addProvider($name, ProviderInterface $provider)
    {
        $this->providers[$name] = $provider;
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getProvider($name)
    {
        if (!$this->hadProvider($name)) {
            throw new \InvalidArgumentException(sprintf('Provider "%s" doesn\'t exist', $name));
        }

        return $this->providers[$name];
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasProvider($name)
    {
        return array_key_exists($name, $this->providers);
    }

    /**
     * @param Filesystem
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
     * @param $name
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function addFilesystem($name, Filesystem $filesystem)
    {
        $this->filesystems[$name] = $filesystem;
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getFilesystem($name)
    {
        if (!$this->hadFilesystem($name)) {
            throw new \InvalidArgumentException(sprintf('Filesystem "%s" doesn\'t exist', $name));
        }

        return $this->filesystems[$name];
    }

    /**
     * @param $name
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
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     * @param bool $new
     */
    public function saveMedia(Media $media, $new = FALSE)
    {
        $context = $this->getContext($media->getContext());
        $context->getProvider()->setFormats($context->getFormats());

        if ($new) {
            $context->getProvider()->saveMedia($media);
        }
        else {
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