<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ namespace }}\Entity\MapItem;
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
class MapPagePart extends AbstractPagePart implements DeepCloneInterface
{
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="\{{ namespace }}\Entity\MapItem", mappedBy="mapPagePart", cascade={"persist", "remove"}, orphanRemoval=true)
     **/
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): MapPagePart
    {
        $this->text = $text;

        return $this;
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

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(MapItem $item)
    {
        $item->setMapPagePart($this);

        $this->items->add($item);
    }

    public function removeItem(MapItem $item)
    {
        $this->items->removeElement($item);
    }

    public function getDefaultView(): string
    {
        return 'pageparts/map_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }

    /**
     * When cloning this entity, also clone all entities in the item ArrayCollection
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
