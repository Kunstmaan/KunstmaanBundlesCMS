<?php

namespace Kunstmaan\KMediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\KMediaBundle\Entity\Image
 * Class that defines a picture in the system
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Table("image")
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
     * @ORM\JoinColumn(name="original", referencedColumnName="id", nullable=true)
     */
    protected $original;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="original")
     */
    protected $edits;

    /**
     * @var string $context
     *
     */
    protected $context = "omnext_picture";

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
     * @var Kunstmaan\KMediaBundle\Entity\Gallery
     */
    protected $gallery;

    /**
     * @var array $metadata
     */
    protected $metadata;

    public function __construct()
    {
        parent::__construct();
        $this->edits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set original
     *
     * @param Kunstmaan\KMediaBundle\Entity\Image $original
     */
    public function setOriginal(Image $original)
    {
        $this->original = $original;
    }

    /**
     * Get original
     *
     * @return Kunstmaan\KMediaBundle\Entity\Image
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Add edits
     *
     * @param Kunstmaan\KMediaBundle\Entity\Image $edits
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
     * @param Kunstmaan\KMediaBundle\Entity\Image $edits
     */
    public function addImage(\Kunstmaan\KMediaBundle\Entity\Image $edits)
    {
        $this->edits[] = $edits;
    }
}