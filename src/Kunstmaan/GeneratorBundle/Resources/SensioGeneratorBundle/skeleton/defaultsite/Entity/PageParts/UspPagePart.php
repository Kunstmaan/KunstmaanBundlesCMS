<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ namespace }}\Entity\UspItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\DeepCloneInterface;
use Symfony\Component\Validator\Constraints as Assert;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}usp_page_parts')]
{% else %}
/**
 * @ORM\Table(name="{{ prefix }}usp_page_parts")
 * @ORM\Entity
 */
{% endif %}
class UspPagePart extends AbstractPagePart implements DeepCloneInterface
{
    /**
     * @var Collection
{% if canUseEntityAttributes == false %}
     *
{% if canUseAttributes == false %}
     * @Assert\Valid()
{% endif %}
     * @ORM\OneToMany(targetEntity="\{{ namespace }}\Entity\UspItem", mappedBy="uspPagePart", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"weight" = "ASC"})
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\Valid]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\OneToMany(targetEntity: UspItem::class, mappedBy: 'uspPagePart', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['weight' => 'ASC'])]
{% endif %}
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @param ArrayCollection $items
     */
    public function setItems($items): void
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(UspItem $item): void
    {
        $item->setUspPagePart($this);

        $this->items->add($item);
    }

    public function removeItem(UspItem $item): void
    {
        $this->items->removeElement($item);
    }

    public function getDefaultView(): string
    {
        return 'PageParts/UspPagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return \{{ namespace }}\Form\PageParts\UspPagePartAdminType::class;
    }

    /**
     * When cloning this entity, we must also clone all entities in the ArrayCollection.
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
