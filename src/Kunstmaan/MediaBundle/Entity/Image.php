<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Kunstmaan\MediaBundle\Entity\Image
 * Class that defines a picture in the system
 *
 * @ORM\Entity
 * @ORM\Table(name="media_image")
 */
class Image extends Media
{

    /**
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="edits")
     * @ORM\JoinColumn(name="original", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $original;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="original", cascade={"persist"})
     */
    protected $edits;

    /**
     * @var string $context
     *
     */
    protected $context = "kunstmaan_media_image";

    public function __construct()
    {
        parent::__construct();
        $this->edits = new ArrayCollection();
        $this->classtype = "Image";
    }

    /**
     * Set original
     *
     * @param Kunstmaan\MediaBundle\Entity\Image $original
     */
    public function setOriginal(Image $original)
    {
        $this->original = $original;
    }

    /**
     * Get original
     *
     * @return Kunstmaan\MediaBundle\Entity\Image
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Add edits
     *
     * @param Kunstmaan\MediaBundle\Entity\Image $edits
     */
    public function addEdits(Image $edits)
    {
        $this->edits[] = $edits;
    }

    /**
     * Get edits
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getEdits()
    {
        return $this->edits;
    }

 	/**
     * Add edits
     *
     * @param Kunstmaan\MediaBundle\Entity\Image $edits
     */
    public function addImage(Image $edits)
    {
        $this->edits[] = $edits;
    }
    
    public function __tostring(){
    	return $this->getName();
    }
}