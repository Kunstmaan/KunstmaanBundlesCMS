<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Helper\FolderStrategy;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Class that defines a folder from the MediaBundle in the database
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\FolderRepository")
 * @ORM\Table(name="kuma_folders")
 * @ORM\HasLifecycleCallbacks
 */
class Folder extends AbstractEntity
{

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    protected $locale;

    /**
     * @var Folder
     *
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="children", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent", fetch="LAZY")
     * @ORM\OrderBy({"sequencenumber" = "ASC"})
     */
    protected $children;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Media", mappedBy="folder")
     */
    protected $media;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $rel;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $sequencenumber;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->media    = new ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
        $this->deleted = false;
    }

    /**
     * @param string $name
     *
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $locale
     *
     * @return Folder
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param string $rel
     *
     * @return Folder
     */
    public function setRel($rel)
    {
        $this->rel = $rel;

        return $this;
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Folder
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Folder
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set parent
     *
     * @param Folder $parent
     *
     * @return Folder
     */
    public function setParent(Folder $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Folder
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Folder[]:
     */
    public function getParents()
    {
        $parent  = $this->getParent();
        $parents = array();
        while ($parent != null) {
            $parents[] = $parent;
            $parent    = $parent->getParent();
        }

        return array_reverse($parents);
    }

    /**
     * Add folder
     *
     * @param Folder $folder
     *
     * @return Folder
     */
    public function addGallery(Folder $folder)
    {
        $this->children[] = $folder;

        return $this;
    }

    /**
     * Add a child
     *
     * @param Folder $child
     *
     * @return Folder
     */
    public function addChild(Folder $child)
    {
        $this->children[] = $child;
        $child->setParent($this);

        return $this;
    }

    /**
     * @param bool $includeDeleted
     *
     * @return Folder[]
     */
    public function getChildren($includeDeleted = false)
    {
        if ($includeDeleted) {
            return $this->children;
        }

        return $this->children->filter( function (Folder $entry) {
            if ($entry->isDeleted()) {
                return false;
            }

            return true;
        });
    }

    /**
     * @return int
     */
    public function getNextSequence()
    {
        $children = $this->getChildren();
        $count    = 0;
        foreach ($children as $child) {
            $count++;
        }

        return $count + 1;
    }

    /**
     * @param array $children
     *
     * @return Folder
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return int
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * @param int $sequencenumber
     *
     * @return Folder
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     *
     * @return Folder
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Add file
     *
     * @param Media $media
     *
     * @return Folder
     */
    public function addMedia(Media $media)
    {
        $this->media[] = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @param bool $includeDeleted
     *
     * @return ArrayCollection
     */
    public function getMedia($includeDeleted = false)
    {
        if ($includeDeleted) {
            return $this->media;
        }

        return $this->media->filter( function (Media $entry) {
            if ($entry->isDeleted()) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function hasActive($id)
    {
        foreach ($this->getChildren() as $child) {
            if ($child->hasActive($id) || $child->getId() == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     */
    public function preInsert()
    {
        if (!$this->sequencenumber) {
            $parent = $this->getParent();
            if ($parent) {
                $count = $parent->getChildren()->count();
                $this->sequencenumber = $count + 1;
            } else {
                $this->sequencenumber = 1;
            }
        }
    }
}