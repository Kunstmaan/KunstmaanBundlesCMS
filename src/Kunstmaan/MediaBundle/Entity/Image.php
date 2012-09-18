<?php

namespace Kunstmaan\MediaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Kunstmaan\MediaBundle\Entity\Image
 * Class that defines a picture in the system
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_media_images")
 */
class Image extends Media
{

    const CONTEXT = "kunstmaan_media_image";

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="edits")
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $original;

    /**
     * @var Image[]
     *
     * @ORM\OneToMany(targetEntity="Image", mappedBy="original", cascade={"persist"})
     */
    protected $edits;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->edits = new ArrayCollection();
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

    /**
     * Set original
     *
     * @param Image $original
     *
     * @return Image
     */
    public function setOriginal(Image $original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return Image
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Add edits
     *
     * @param Image $edits
     *
     * @return Image
     */
    public function addEdits(Image $edits)
    {
        $this->edits[] = $edits;

        return $this;
    }

    /**
     * Get edits
     *
     * @return ArrayCollection
     */
    public function getEdits()
    {
        return $this->edits;
    }

    /**
     * Add edits
     *
     * @param Image $edits
     *
     * @return Image
     */
    public function addImage(Image $edits)
    {
        $this->edits[] = $edits;

        return $this;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->getName();
    }
}
