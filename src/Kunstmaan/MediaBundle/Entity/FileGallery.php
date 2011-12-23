<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\FileGalleryRepository")
 * @ORM\Table(name="media_gallery_file")
 * @ORM\HasLifecycleCallbacks
 */
class FileGallery extends Gallery{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $slug
     */
    protected $slug;

    /**
     * @var datetime $created
     */
    protected $created;

    /**
     * @var datetime $updated
     */
    protected $updated;

    /**
     * @var Kunstmaan\MediaBundle\Entity\Gallery
     */
    protected $parent;

    /**
     * @var Kunstmaan\MediaBundle\Entity\Gallery
     */
    protected $children;

    /**
     * @var Kunstmaan\MediaBundle\Entity\Media
     */
    protected $files;

    /**
     * @var string $content
     */
    protected $content;

    public function setId($id){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getStrategy(){
        return new \Kunstmaan\MediaBundle\Helper\FileGalleryStrategy();
    }
}