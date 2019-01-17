<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ db_prefix }}gallery_row_media_items")
 * @ORM\Entity
 */
class GalleryRowMediaItem extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * })
     * @Assert\NotNull()
     */
    private $media;

    /**
     * @ORM\Column(name="weight", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\GalleryRow", inversedBy="mediaItems")
     * @ORM\JoinColumn(name="gallery_row_id", referencedColumnName="id")
     **/
    private $galleryRow;

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): GalleryRowMediaItem
    {
        $this->media = $media;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): GalleryRowMediaItem
    {
        $this->weight = $weight;

        return $this;
    }

    public function getGalleryRow(): ?GalleryRow
    {
        return $this->galleryRow;
    }

    public function setGalleryRow($galleryRow): GalleryRowMediaItem
    {
        $this->galleryRow = $galleryRow;

        return $this;
    }
}
