<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Node as GedmoNode;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Defines a folder from the MediaBundle in the database
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\FolderRepository")
 * @ORM\Table(name="kuma_folders", indexes={
 *      @ORM\Index(name="idx_folder_internal_name", columns={"internal_name"}),
 *      @ORM\Index(name="idx_folder_name", columns={"name"}),
 *      @ORM\Index(name="idx_folder_deleted", columns={"deleted"})
 * })
 * @Gedmo\Tree(type="nested")
 * @ORM\HasLifecycleCallbacks
 */
#[ORM\Entity(repositoryClass: FolderRepository::class)]
#[ORM\Table(name: 'kuma_folders')]
#[ORM\Index(name: 'idx_folder_internal_name', columns: ['internal_name'])]
#[ORM\Index(name: 'idx_folder_name', columns: ['name'])]
#[ORM\Index(name: 'idx_folder_deleted', columns: ['deleted'])]
#[Gedmo\Tree(type: 'nested')]
#[ORM\HasLifecycleCallbacks]
class Folder extends AbstractEntity implements GedmoNode
{
    const TYPE_FILES = 'files';
    const TYPE_IMAGE = 'image';
    const TYPE_MEDIA = 'media';
    const TYPE_SLIDESHOW = 'slideshow';
    const TYPE_VIDEO = 'video';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="string")
     */
    #[ORM\Column(name: 'name', type: 'string')]
    #[Gedmo\Translatable]
    #[Assert\NotBlank]
    protected $name;

    /**
     * @var string
     *
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    #[Gedmo\Locale]
    protected $locale;

    /**
     * @var Folder
     *
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="children", fetch="LAZY")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     * @Gedmo\TreeParent
     */
    #[ORM\ManyToOne(targetEntity: Folder::class, inversedBy: 'children', fetch: 'LAZY')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true)]
    #[Gedmo\TreeParent]
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent", fetch="LAZY")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    #[ORM\OneToMany(targetEntity: Folder::class, mappedBy: 'parent', fetch: 'LAZY')]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    protected $children;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Media", mappedBy="folder", fetch="LAZY")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'folder', fetch: 'LAZY')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected $media;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at")
     */
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    protected $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    #[ORM\Column(name: 'rel', type: 'string', nullable: true)]
    protected $rel;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="internal_name", nullable=true)
     */
    #[ORM\Column(name: 'internal_name', type: 'string', nullable: true)]
    protected $internalName;

    /**
     * @var int
     *
     * @ORM\Column(name="lft", type="integer", nullable=true)
     * @Gedmo\TreeLeft
     */
    #[ORM\Column(name: 'lft', type: 'integer', nullable: true)]
    #[Gedmo\TreeLeft]
    protected $lft;

    /**
     * @var int
     *
     * @ORM\Column(name="lvl", type="integer", nullable=true)
     * @Gedmo\TreeLevel
     */
    #[ORM\Column(name: 'lvl', type: 'integer', nullable: true)]
    #[Gedmo\TreeLevel]
    protected $lvl;

    /**
     * @var int
     *
     * @ORM\Column(name="rgt", type="integer", nullable=true)
     * @Gedmo\TreeRight
     */
    #[ORM\Column(name: 'rgt', type: 'integer', nullable: true)]
    #[Gedmo\TreeRight]
    protected $rgt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(name: 'deleted', type: 'boolean')]
    protected $deleted;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->deleted = false;
    }

    /**
     * @return array
     */
    public static function allTypes()
    {
        return [
            self::TYPE_MEDIA => self::TYPE_MEDIA,
            self::TYPE_IMAGE => self::TYPE_IMAGE,
            self::TYPE_FILES => self::TYPE_FILES,
            self::TYPE_SLIDESHOW => self::TYPE_SLIDESHOW,
            self::TYPE_VIDEO => self::TYPE_VIDEO,
        ];
    }

    /**
     * @return string
     */
    public function getTranslatableLocale()
    {
        return $this->locale;
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
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Folder
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Folder
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Folder[]
     */
    public function getParents()
    {
        $parent = $this->getParent();
        $parents = [];
        while ($parent !== null) {
            $parents[] = $parent;
            $parent = $parent->getParent();
        }

        return array_reverse($parents);
    }

    /**
     * @return Folder
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Folder
     */
    public function setParent(Folder $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Folder
     */
    public function addChild(Folder $child)
    {
        $this->children[] = $child;
        $child->setParent($this);

        return $this;
    }

    /**
     * @return Folder
     */
    public function addMedia(Media $media)
    {
        $this->media[] = $media;

        return $this;
    }

    /**
     * @param bool $includeDeleted
     *
     * @return ArrayCollection
     */
    public function getMedia($includeDeleted = false)
    {
        if ($includeDeleted) {
            return $this->media;
        }

        return $this->media->filter(
            function (Media $entry) {
                if ($entry->isDeleted()) {
                    return false;
                }

                return true;
            }
        );
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
     * @param bool $includeDeleted
     *
     * @return ArrayCollection
     */
    public function getChildren($includeDeleted = false)
    {
        if ($includeDeleted) {
            return $this->children;
        }

        return $this->children->filter(
            function (Folder $entry) {
                if ($entry->isDeleted()) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * @param ArrayCollection $children
     *
     * @return Folder
     */
    public function setChildren($children)
    {
        $this->children = $children;

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
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @param string $internalName
     *
     * @return Folder
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @param int $lft
     *
     * @return Folder
     */
    public function setLeft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->lft;
    }

    /**
     * @param int $lvl
     *
     * @return Folder
     */
    public function setLevel($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * @param int $rgt
     *
     * @return Folder
     */
    public function setRight($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->rgt;
    }

    /**
     * @return string
     */
    public function getOptionLabel()
    {
        return str_repeat(
            '-',
            $this->getLevel()
        ) . ' ' . $this->getName();
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->lvl;
    }

    /**
     * @ORM\PreUpdate
     */
    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}
