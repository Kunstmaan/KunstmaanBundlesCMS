<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminBundle\Entity\DeepCloneInterface;
use {{ namespace }}\Entity\UspItem;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ prefix }}usp_page_parts")
 * @ORM\Entity
 */
class UspPagePart extends AbstractPagePart implements DeepCloneInterface
{
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\{{ namespace }}\Entity\UspItem", mappedBy="uspPagePart", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"weight" = "ASC"})
     **/
    private $items;

    /**
     * Constructor
     */
    public function __construct()
    {
	$this->items = new ArrayCollection();
    }

    /**
     * @param ArrayCollection $items
     */
    public function setItems($items)
    {
	foreach ($items as $item) {
	    $this->addItem($item);
	}
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
	return $this->items;
    }

    /**
     * @param UspItem $item
     */
    public function addItem(UspItem $item)
    {
	$item->setUspPagePart($this);

	$this->items->add($item);
    }

    /**
     * @param UspItem $item
     */
    public function removeItem(UspItem $item)
    {
	$this->items->removeElement($item);
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return '{{ bundle.getName() }}:PageParts:UspPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \{{ namespace }}\Form\PageParts\UspPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \{{ namespace }}\Form\PageParts\UspPagePartAdminType();
    }

    /**
     * When cloning this entity, we must also clone all entities in the ArrayCollection
     */
    public function deepClone()
    {
	$items = $this->getItems();
	$this->items = new ArrayCollection();
	foreach ($items as $item) {
	    $cloneItem = clone $item;
	    $this->addItem($cloneItem);
	}
    }
}
