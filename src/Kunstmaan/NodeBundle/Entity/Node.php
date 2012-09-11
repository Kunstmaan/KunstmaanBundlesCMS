<?php

namespace Kunstmaan\AdminNodeBundle\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Kunstmaan\AdminNodeBundle\Form\NodeAdminType;

/**
 * Node
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminNodeBundle\Repository\NodeRepository")
 * @ORM\Table(name="node")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Node extends AbstractEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $sequencenumber;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent")
     * @ORM\OrderBy({"sequencenumber" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="NodeTranslation", mappedBy="node")
     */
    protected $nodeTranslations;

    /**
     * @ORM\Column(type="array", nullable=false)
     */
    protected $roles;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hiddenFromNav;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $refEntityname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $internalName;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->children         = new ArrayCollection();
        $this->nodeTranslations = new ArrayCollection();
        $this->deleted          = false;
        $this->hiddenFromNav    = false;
    }

    /**
     * @return boolean
     */
    public function isHiddenFromNav()
    {
        return $this->hiddenFromNav;
    }

    /**
     * @return boolean
     */
    public function getHiddenFromNav()
    {
        return $this->hiddenFromNav;
    }

    /**
     * @param boolean $hiddenFromNav
     */
    public function setHiddenFromNav($hiddenFromNav)
    {
        $this->hiddenFromNav = $hiddenFromNav;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children->filter(
            function ($entry) {
                if ($entry->isDeleted()) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * @param ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Add children
     *
     * @param Node $child
     */
    public function addNode(Node $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    public function disableChildrenLazyLoading()
    {
        if (is_object($this->children)) {
            $this->children->setInitialized(true);
        }
    }

    /**
     * @param boolean $includeOffline
     *
     * @return ArrayCollection
     */
    public function getNodeTranslations($includeOffline = false)
    {
        return $this->nodeTranslations
            ->filter(
            function ($entry) use ($includeOffline) {
                if ($includeOffline || $entry->isOnline()) {
                    return true;
                }

                return false;
            }
        );
    }

    /**
     * @param ArrayCollection $nodeTranslations
     */
    public function setNodeTranslations($nodeTranslations)
    {
        $this->nodeTranslations = $nodeTranslations;
    }

    /**
     * @param string  $lang
     * @param boolean $includeOffline
     *
     * @return NodeTranslation|null
     */
    public function getNodeTranslation($lang, $includeOffline = false)
    {
        $nodeTranslations = $this->getNodeTranslations($includeOffline);
        foreach ($nodeTranslations as $nodeTranslation) {
            if ($lang == $nodeTranslation->getLang()) {
                return $nodeTranslation;
            }
        }

        return null;
    }

    /**
     * Add nodeTranslation
     *
     * @param NodeTranslation $nodeTranslation
     *
     * @todo Shouldn't we add a check to prevent adding duplicates here?
     */
    public function addNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $this->nodeTranslations[] = $nodeTranslation;
        $nodeTranslation->setNode($this);
    }

    public function disableNodeTranslationsLazyLoading()
    {
        if (is_object($this->nodeTranslations)) {
            $this->nodeTranslations->setInitialized(true);
        }
    }

    /**
     * Set parent
     *
     * @param Node $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Node[]
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
     * @param integer $sequencenumber
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;
    }

    /**
     * @return integer
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Set refEntityname
     *
     * @param string $refEntityname
     */
    public function setRefEntityname($refEntityname)
    {
        $this->refEntityname = $refEntityname;
    }

    /**
     * Get refEntityname
     *
     * @return string
     */
    public function getRefEntityname()
    {
        return $this->refEntityname;
    }

    /**
     * Set internalName
     *
     * @param string $internalName
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;
    }

    /**
     * Get internalName
     *
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @param $container
     *
     * @return NodeAdminType
     */
    public function getDefaultAdminType()
    {
        return new NodeAdminType();
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

    public function __toString()
    {
        return "node " . $this->getId() . ", refEntityname: " . $this->getRefEntityname();
    }
}
