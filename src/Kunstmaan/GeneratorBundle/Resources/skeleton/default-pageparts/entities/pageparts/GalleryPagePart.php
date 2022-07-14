<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ namespace }}\Entity\GalleryRow;
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
class GalleryPagePart extends AbstractPagePart implements DeepCloneInterface
{
    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="\{{ namespace }}\Entity\GalleryRow", mappedBy="galleryPagePart", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"weight" = "ASC"})
     **/
    private $rows;

    public function __construct()
    {
        $this->rows = new ArrayCollection();
    }

    public function getRows(): Collection
    {
        return $this->rows;
    }

    public function addRow(GalleryRow $row)
    {
        if (!$this->rows->contains($row)) {
            $this->rows->add($row);
            $row->setGalleryPagePart($this);
        }

        return $this;
    }

    public function removeRow(GalleryRow $row)
    {
        if ($this->rows->contains($row)) {
            $this->rows->removeElement($row);
        }

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/gallery_pagepart/view.html.twig';
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
        /** @var Collection|GalleryRow[] $rows */
        $rows = $this->getRows();
        $this->rows = new ArrayCollection();
        foreach ($rows as $row) {
            $cloneRow = clone $row;
            $cloneRow->deepClone();

            $this->addRow($cloneRow);
        }
    }
}
