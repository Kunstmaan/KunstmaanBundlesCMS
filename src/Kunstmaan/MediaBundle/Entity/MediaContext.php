<?php

namespace Kunstmaan\MediaBundle\Entity;

use Kunstmaan\MediaBundle\Helper\Provider\ProviderInterface;

class MediaContext
{
    /* @var string */
    protected $name;

    /* @var ProviderInterface */
    protected $provider;

    /* @var array */
    protected $formats = array();


    /**
     * @var AbstractMediaMetadata
     */
    protected $metadataClass;


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
     * @param \Ano\Bundle\MediaBundle\Provider\ProviderInterface $provider
     */
    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return \Ano\Bundle\MediaBundle\Provider\ProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $name
     * @param array $format
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
     * @return boolean
     */
    public function hasFormat($name)
    {
        return array_key_exists($name, $this->formats);
    }

    /**
     * @param string $name
     *
     * @return string|boolean
     */
    public function getFormat($name)
    {
        return $this->hasFormat($name) ? $this->formats[$name] : FALSE;
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
     *
     * @param \Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata $metadataClass
     */
    public function setMetadataClass($metadataClass)
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