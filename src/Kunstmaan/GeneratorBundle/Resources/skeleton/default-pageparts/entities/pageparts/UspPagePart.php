<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ namespace }}\Entity\UspItem;
use {{ admin_type_full }};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\DeepCloneInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ table_name }}")
 * @ORM\Entity
 */
class UspPagePart extends AbstractPagePart implements DeepCloneInterface
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="\{{ namespace }}\Entity\UspItem", mappedBy="uspPagePart", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"weight" = "ASC"})
     **/
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): UspPagePart
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|UspItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(UspItem $item): UspPagePart
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setUspPagePart($this);
        }

        return $this;
    }

    public function removeItem(UspItem $item): UspPagePart
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/usp_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }

    /**
     * When cloning this entity, also clone all entities in the item ArrayCollection.
     */
    public function deepClone(): void
    {
        $items = $this->getItems();
        $this->items = new ArrayCollection();
        foreach ($items as $item) {
            $cloneItem = clone $item;
            $this->addItem($cloneItem);
        }
    }
}
