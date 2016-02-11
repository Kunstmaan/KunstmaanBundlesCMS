<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use {{ namespace }}\Entity\PageParts\UspPagePart;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ prefix }}usp_item")
 * @ORM\Entity
 */
class UspItem extends AbstractEntity
{
    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     * })
     * @Assert\NotNull()
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity="\{{ namespace }}\Entity\PageParts\UspPagePart", inversedBy="items")
     * @ORM\JoinColumn(name="usp_pp_id", referencedColumnName="id")
     **/
    private $uspPagePart;

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $icon
     */
    public function setIcon($icon)
    {
	$this->icon = $icon;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getIcon()
    {
	return $this->icon;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
	return $this->title;
    }

    /**
     * @param string $title
     *
     * @return UspItem
     */
    public function setTitle($title)
    {
	$this->title = $title;

	return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
	return $this->description;
    }

    /**
     * @param string $description
     *
     * @return UspItem
     */
    public function setDescription($description)
    {
	$this->description = $description;

	return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
	return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return UspItem
     */
    public function setWeight($weight)
    {
	$this->weight = $weight;

	return $this;
    }

    /**
     * @param UspPagePart $uspPagePart
     */
    public function setUspPagePart(UspPagePart $uspPagePart)
    {
	$this->uspPagePart = $uspPagePart;
    }

    /**
     * @return UspPagePart
     */
    public function getUspPagePart()
    {
	return $this->uspPagePart;
    }
}
