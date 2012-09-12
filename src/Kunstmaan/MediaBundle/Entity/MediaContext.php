<?php

namespace Kunstmaan\MediaBundle\Entity;

use Kunstmaan\MediaBundle\Helper\Provider\ProviderInterface;

/**
 * MediaContext
 */
class MediaContext
{
    /**
     *  @var string
     */
    protected $name;

    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @var array
     */
    protected $formats = array();


    /**
     * @var AbstractMediaMetadata
     */
    protected $metadataClass;

    /**
     * constructor
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param ProviderInterface $provider
     */
    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $name   The name
     * @param array  $format The format
     *
     * @return void
     */
    public function addFormat($name, array $format)
    {
        $this->formats[$name] = $format;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFormat($name)
    {
        return array_key_exists($name, $this->formats);
    }

    /**
     * @param string $name
     *
     * @return string|bool
     */
    public function getFormat($name)
    {
        return $this->hasFormat($name) ? $this->formats[$name] : false;
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * Set metadataClass
     * @param AbstractMediaMetadata $metadataClass
     *
     * @return MediaContext
     */
    public function setMetadataClass(AbstractMediaMetadata $metadataClass)
    {
        $this->metadataClass = $metadataClass;

        return $this;
    }

    /**
     * Get metadataClass
     *
     * @return \Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata
     */
    public function getMetadataClass()
    {
        return $this->metadataClass;
    }
}