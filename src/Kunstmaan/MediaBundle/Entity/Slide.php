<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Slide
 * Class that defines a slide in the system
 *
 * @ORM\Entity
 * @ORM\Table(name="media_slide")
 */
class Slide extends Media
{

    const CONTEXT = "kunstmaan_media_code";

    /**
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
    public function show($format=null, $options = array())
    {
        return '<script src="http://speakerdeck.com/embed/'. $this->metadata['uuid'] .'.js"></script>';
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
     *
     * @param string $slidetype
     */
    public function setType($slidetype)
    {
        $this->type = $slidetype;
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