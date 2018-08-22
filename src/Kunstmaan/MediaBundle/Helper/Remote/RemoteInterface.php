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
     *
     * @return mixed
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getCopyright();

    /**
     * @param string $copyright
     *
     * @return mixed
     */
    public function setCopyright($copyright);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     *
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     *
     * @return mixed
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getThumbnailUrl();

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function setThumbnailUrl($url);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function setType($type);
}
