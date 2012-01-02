<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Image
 * Class that defines a picture in the system
 *
 * @author Kristof Van Cauwenbergh
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

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $contentType
     */
    protected $contentType;

    /**
     * @var datetime $createdAt
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     */
    protected $updatedAt;

    protected $content;

    /**
     * @var Kunstmaan\MediaBundle\Entity\Gallery
     */
    protected $gallery;
    
    protected $type;

    /**
     * @var array $metadata
     */
    protected $metadata;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return $this->uuid;
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
}