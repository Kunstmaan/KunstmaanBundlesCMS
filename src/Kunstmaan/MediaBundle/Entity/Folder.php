<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\AdminBundle\Helper\Slugifier;
use Kunstmaan\MediaBundle\Helper\FolderStrategy;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Class that defines a folder from the MediaBundle in the database
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\FolderRepository")
 * @ORM\Table(name="kuma_media_folders")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({ "folder"="Folder", "imagegallery" = "ImageGallery", "filegallery" = "FileGallery", "slidegallery" = "SlideGallery" , "videogallery" = "VideoGallery"})
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
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
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $slug;

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
     * @ORM\OneToMany(targetEntity="Media", mappedBy="gallery")
     */
    protected $files;

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
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $candelete;

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
        $this->files    = new ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
        $this->setCanDelete(true);
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
        $this->setSlug($this->name);

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
     * Set slug
     *
     * @param string $slug
     *
     * @return Folder
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param bool $bool
     *
     * @return Folder
     */
    public function setCanDelete($bool)
    {
        $this->candelete = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        return $this->candelete;
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
     * @param Media $file
     *
     * @return Folder
     */
    public function addMedia(Media $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Get files
     *
     * @param bool $includeDeleted
     *
     * @return ArrayCollection
     */
    public function getFiles($includeDeleted = false)
    {
        if ($includeDeleted) {
            return $this->files;
        }

        return $this->files->filter( function (File $entry) {
            if ($entry->isDeleted()) {
                return false;
            }

            return true;
        });
    }

    /**
     * Get images
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->getFiles()->filter( function ($file) {
            if ($file instanceof Image) {
                return true;
            }

            return false;
        });
    }

    /**
     * @return bool
     */
    public function hasImages()
    {
        if (count($this->getImages()) > 0) {
            return true;
        }
        foreach ($this->getChildren() as $child) {
            if ($child->hasImages()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayCollection
     */
    public function getFilesOnly()
    {
        return $this->getFiles()->filter( function ($file) {
            if ($file instanceof File) {
                return true;
            }

            return false;
        });
    }

    /**
     * @return bool
     */
    public function hasFiles()
    {
        if (count($this->getFilesOnly()) > 0) {
            return true;
        }
        foreach ($this->getChildren() as $child) {
            if ($child->hasFiles()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayCollection
     */
    public function getSlidesOnly()
    {
        return $this->getFiles()->filter( function ($file) {
            if ($file instanceof Slide) {
                return true;
            }

            return false;
        });
    }

    /**
     * @return bool
     */
    public function hasSlides()
    {
        if (count($this->getSlidesOnly()) > 0) {
            return true;
        }
        foreach ($this->getChildren() as $child) {
            if ($child->hasSlides()) {
                return true;
            }
        }

        return true;
    }

    /**
     * @return ArrayCollection
     */
    public function getVideosOnly()
    {
        return $this->getFiles()->filter( function ($file) {
            if ($file instanceof Video) {
                return true;
            }

            return false;
        });
    }

    /**
     * @return bool
     */
    public function hasVideos()
    {
        if (count($this->getVideosOnly()) > 0) {
            return true;
        }
        foreach ($this->getChildren() as $child) {
            if ($child->hasVideos()) {
                return true;
            }
        }

        return false;
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
     * @return FolderStrategy
     */
    public function getStrategy()
    {
        return new FolderStrategy();
    }

    /**
     * @param Folder $folder
     *
     * @return FolderType
     */
    public function getFormType(Folder $folder = null)
    {
        return new FolderType($this->getStrategy()->getGalleryClassName(), $folder);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getStrategy()->getType();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setSlug(\Kunstmaan\AdminBundle\Modules\Slugifier::slugify($this->getName()));
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {

    }

    /**
     * @ORM\PrePersist
     */
    public function preInsert()
    {
        if (!$this->sequencenumber) {
            $parent = $this->getParent();
            if ($parent) {
                $count                = $parent->getChildren()->count();
                $this->sequencenumber = $count + 1;
            } else {
                $this->sequencenumber = 1;
            }
        }
    }
}