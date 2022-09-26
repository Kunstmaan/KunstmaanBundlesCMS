<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\ContentPageAdminType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

{% if canUseEntityAttributes %}
#[ORM\Entity()]
#[ORM\Table(name: '{{ prefix }}content_pages')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}content_pages")
 */
{% endif %}
class ContentPage extends AbstractPage implements HasPageTemplateInterface, SearchTypeInterface
{
{% if demosite %}
    /**
     * @var Media|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="menu_image_id", referencedColumnName="id")
     * })
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: Media::class)]
{% endif %}
    private $menuImage;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="menu_description", type="text", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'menu_description', type: Types::TEXT, nullable: true)]
{% endif %}
    private $menuDescription;
{% endif %}

    public function getDefaultAdminType(): string
    {
        return ContentPageAdminType::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [
            [
                'name' => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage',
            ],
        ];
    }

{% if demosite %}
    public function setMenuImage(?Media $image): void
    {
        $this->menuImage = $image;
    }

    public function getMenuImage(): ?Media
    {
        return $this->menuImage;
    }

    public function getMenuDescription(): ?string
    {
        return $this->menuDescription;
    }

    public function setMenuDescription(?string $description): ContentPage
    {
        $this->menuDescription = $description;

        return $this;
    }
{% endif %}

    public function getSearchType(): string
    {
        return 'Page';
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['main'];
    }

    public function getPageTemplates(): array
    {
        return ['contentpage'{% if demosite %}, 'contentpage_with_submenu'{% endif %}];
    }

    public function getDefaultView(): string
    {
        return 'Pages/ContentPage/view.html.twig';
    }
}
