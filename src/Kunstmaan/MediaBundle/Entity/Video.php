<?php

namespace Kunstmaan\MediaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Video
 * Class that defines a video in the system
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_videos")
 */
class Video extends Media
{

    /**
     * @var string
     */
    const CONTEXT = "kunstmaan_media_code";

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this::CONTEXT;
    }

    /**
     * @param string $format  format
     * @param array  $options options
     *
     * @return string
     */
    public function show($format = null, $options = array())
    {
        return '';
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->metadata['uuid'];
    }

    /**
     * Set slidetype
     * @param string $type
     *
     * @return Video
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get slidetype
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
