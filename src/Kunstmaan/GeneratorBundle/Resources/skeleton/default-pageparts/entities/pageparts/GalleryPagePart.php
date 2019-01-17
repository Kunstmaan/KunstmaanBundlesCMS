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

    /**
     * @param ArrayCollection $rows
     */
    public function setItems($rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    public function getRows(): Collection
    {
        return $this->rows;
    }

    public function addRow(GalleryRow $row)
    {
        $row->setGalleryPagePart($this);

        $this->rows->add($row);
    }

    public function removeRow(GalleryRow $row)
    {
        $this->rows->removeElement($row);
    }

    public function getDefaultView(): string
    {
        return 'pageparts/Gallery_pagepart/view.html.twig';
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
        $rows = $this->getRows();
        $this->rows = new ArrayCollection();
        foreach ($rows as $row) {
            $cloneRow = clone $row;
            $this->addRow($cloneRow);
        }
    }
}
