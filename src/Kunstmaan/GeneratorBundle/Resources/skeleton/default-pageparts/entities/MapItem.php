<?php

namespace {{ namespace }}\Entity;

use {{ namespace }}\Entity\PageParts\MapPagePart;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ db_prefix }}map_items")
 * @ORM\Entity
 */
class MapItem extends AbstractEntity
{
    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     * @Assert\NotNull()
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8)
     * @Assert\NotNull()
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity="\{{ namespace }}\Entity\PageParts\MapPagePart", inversedBy="items")
     * @ORM\JoinColumn(name="map_pp_id", referencedColumnName="id")
     **/
    private $mapPagePart;

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): MapItem
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): MapItem
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function setMapPagePart(MapPagePart $mapPagePart): MapItem
    {
        $this->mapPagePart = $mapPagePart;

        return $this;
    }

    public function getMapPagePart(): ?MapPagePart
    {
        return $this->mapPagePart;
    }
}
