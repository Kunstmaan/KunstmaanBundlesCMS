<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Image
 * Class that defines a picture in the system
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Table("media_file")
 * @ORM\Entity
 */
class File extends Media
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uuid;

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