<?php

namespace {{ namespace }}\Entity;

use {{ namespace }}\Entity\PageParts\UspPagePart;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var Media|null
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
     * @var string|null
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
     * @var string|null
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
     * @var int
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

    public function setIcon(?Media $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?Media
    {
        return $this->icon;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function setUspPagePart(UspPagePart $uspPagePart): void
    {
        $this->uspPagePart = $uspPagePart;
    }

    public function getUspPagePart(): UspPagePart
    {
        return $this->uspPagePart;
    }
}
