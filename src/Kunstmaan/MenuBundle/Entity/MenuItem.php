<?php

namespace Kunstmaan\MenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="kuma_menu_item")
 * @ORM\Entity(repositoryClass="Kunstmaan\MenuBundle\Repository\MenuItemRepository")
 * @Gedmo\Tree(type="nested")
 * @Assert\Callback(methods={"validateEntity"})
 */
class MenuItem extends BaseMenuItem
{
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @var MenuItem
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }
    /**
     * @return MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param MenuItem $parent
     *
     * @return MenuItem
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }
}
