<?php

namespace {{ namespace }}\Entity;

use {{ namespace }}\Entity\PageParts\UspPagePart;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ db_prefix }}usp_items")
 * @ORM\Entity
 */
class UspItem extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     * })
     * @Assert\NotNull()
     */
    private $icon;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="link_text", type="string", length=255, nullable=true)
     */
    private $linkText;

    /**
     * @ORM\Column(name="link_url", type="string", nullable=true)
     */
    private $linkUrl;

    /**
     * @ORM\Column(name="link_new_window", type="boolean", nullable=true)
     */
    private $linkNewWindow = false;

    /**
     * @ORM\Column(name="weight", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity="\{{ namespace }}\Entity\PageParts\UspPagePart", inversedBy="items")
     * @ORM\JoinColumn(name="usp_pp_id", referencedColumnName="id")
     **/
    private $uspPagePart;

    public function setIcon(Media $icon): UspItem
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?Media
    {
        return $this->icon;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): UspItem
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): UspItem
    {
        $this->description = $description;

        return $this;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkText(string $linkText): UspItem
    {
        $this->linkText = $linkText;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(string $linkUrl): UspItem
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    public function getLinkNewWindow(): bool
    {
        return $this->linkNewWindow;
    }

    public function setLinkNewWindow(bool $linkNewWindow): UspItem
    {
        $this->linkNewWindow = $linkNewWindow;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): UspItem
    {
        $this->weight = $weight;

        return $this;
    }

    public function setUspPagePart(UspPagePart $uspPagePart): UspItem
    {
        $this->uspPagePart = $uspPagePart;

        return $this;
    }

    public function getUspPagePart(): ?UspPagePart
    {
        return $this->uspPagePart;
    }
}
