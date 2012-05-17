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

    /**
     * @var string $context
     *
     */
    protected $context = "kunstmaan_media_file";

    public function __construct()
    {
        parent::__construct();
        $this->classtype = "File";
    }
}