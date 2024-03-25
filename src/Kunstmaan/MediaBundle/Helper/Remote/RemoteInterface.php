<?php

namespace Kunstmaan\MediaBundle\Helper\Remote;

interface RemoteInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getCopyright();

    /**
     * @param string $copyright
     */
    public function setCopyright($copyright);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string|null
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string|null
     */
    public function getThumbnailUrl();

    /**
     * @param string $url
     */
    public function setThumbnailUrl($url);

    /**
     * @return string|null
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);
}
