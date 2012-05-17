<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\File
 * Class that defines a picture in the system
 *
 * @ORM\Entity
 * @ORM\Table(name="media_file")
 */
class File extends Media
{

    const CONTEXT = "kunstmaan_media_file";

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
}