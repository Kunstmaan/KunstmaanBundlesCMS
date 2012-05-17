<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Video
 * Class that defines a video in the system
 *
 * @ORM\Entity
 * @ORM\Table(name="media_video")
 */
class Video extends Media
{

    const CONTEXT = "kunstmaan_media_code";

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this::CONTEXT;
    }

 	public function show($format=null, $options = array())
    {
        return '';
    }

    public function getCode(){
        return $this->metadata['uuid'];
    }

    /**
     * Set slidetype
     *
     * @param string $slidetype
     */
    public function setType($type)
    {
        $this->type = $type;
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