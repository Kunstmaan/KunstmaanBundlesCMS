<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Image
 * Class that defines a picture in the system
 *
 * @ORM\Table("media_image")
 * @ORM\Entity
 */
class Image extends Media
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
        $this->edits = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function addImage(\Kunstmaan\MediaBundle\Entity\Image $edits)
    {
        $this->edits[] = $edits;
    }
    
    public function __tostring(){
    	return $this->getName();
    }
}