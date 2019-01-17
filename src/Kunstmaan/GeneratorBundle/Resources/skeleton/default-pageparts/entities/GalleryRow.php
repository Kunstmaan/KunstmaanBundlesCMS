<?php

namespace {{ namespace }}\Entity;

use {{ namespace }}\Entity\PageParts\GalleryPagePart;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ db_prefix }}gallery_rows")
 * @ORM\Entity
 */
class GalleryRow extends AbstractEntity
{
    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="\{{ namespace }}\Entity\GalleryRowMediaItem", mappedBy="galleryRow", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"weight" = "ASC"})
     **/
    private $mediaItems;

    /**
     * @ORM\ManyToOne(targetEntity="\{{ namespace }}\Entity\PageParts\GalleryPagePart", inversedBy="rows")
     * @ORM\JoinColumn(name="gallery_pp_id", referencedColumnName="id")
     **/
    private $galleryPagePart;

    /**
     * @ORM\Column(name="weight", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $weight;

    public function __construct()
    {
        $this->mediaItems = new ArrayCollection();
    }

    public function setGalleryPagePart(GalleryPagePart $galleryPagePart): GalleryRow
    {
        $this->galleryPagePart = $galleryPagePart;

        return $this;
    }

    public function getGalleryPagePart(): ?GalleryRow
    {
        return $this->galleryPagePart;
    }

    /**
     * @param ArrayCollection $items
     */
    public function setMediaItems($items)
    {
        foreach ($items as $item) {
            $this->addMediaItem($item);
        }
    }

    public function getMediaItems(): ?Collection
    {
        return $this->mediaItems;
    }

    public function addMediaItem(GalleryRowMediaItem $item)
    {
        $item->setGalleryRow($this);
        $this->mediaItems->add($item);
    }

    public function removeMediaItem(GalleryRowMediaItem $item)
    {
        $this->mediaItems->removeElement($item);
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): GalleryRow
    {
        $this->weight = $weight;

        return $this;
    }
}
