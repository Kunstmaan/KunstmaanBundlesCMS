<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;
use {{ namespace }}\Entity\PageParts\UspPagePart;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}usp_item')]
{% else %}
/**
 * @ORM\Table(name="{{ prefix }}usp_item")
 * @ORM\Entity
 */
{% endif %}
class UspItem extends AbstractEntity
{
    /**
     * @var Media
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     * })
{% if canUseAttributes == false %}
     * @Assert\NotNull()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotNull]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'icon_id', referencedColumnName: 'id')]
{% endif %}
    private $icon;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: true)]
{% endif %}
    private $title;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="description", type="text", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
{% endif %}
    private $description;

    /**
     * @var integer
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'weight', type: 'integer', nullable: true)]
{% endif %}
    private $weight;

    /**
     * @var UspPagePart
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="\{{ namespace }}\Entity\PageParts\UspPagePart", inversedBy="items")
     * @ORM\JoinColumn(name="usp_pp_id", referencedColumnName="id")
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: UspPagePart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'usp_pp_id', referencedColumnName: 'id')]
{% endif %}
    private $uspPagePart;

    /**
     * @param Media $icon
     */
    public function setIcon($icon)
    {
	$this->icon = $icon;
    }

    /**
     * @return Media
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
