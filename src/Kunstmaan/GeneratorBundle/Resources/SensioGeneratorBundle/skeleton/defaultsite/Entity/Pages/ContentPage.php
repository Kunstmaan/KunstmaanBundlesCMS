<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\Pages\ContentPageAdminType;

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
     * @var Media
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
     * @var string
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

    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return ContentPageAdminType::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array (
            array(
                'name'  => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage'
            ),
        );
    }

{% if demosite %}
    /**
     * @param Media $icon
     */
    public function setMenuImage($image)
    {
        $this->menuImage = $image;
    }

    /**
     * @return Media
     */
    public function getMenuImage()
    {
        return $this->menuImage;
    }

    /**
     * @return string
     */
    public function getMenuDescription()
    {
        return $this->menuDescription;
    }

    /**
     * @param string $description
     *
     * @return ContentPage
     */
    public function setMenuDescription($description)
    {
        $this->menuDescription = $description;

        return $this;
    }
{% endif %}

    /**
     * {@inheritdoc}
     */
    public function getSearchType()
    {
        return 'Page';
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array('{% if not isV4 %}{{ bundle.getName() }}:{%endif%}main');
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array('{% if not isV4 %}{{ bundle.getName() }}:{%endif%}contentpage'{% if demosite %}, '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}contentpage{% if isV4 %}_{% else %}-{% endif %}with{% if isV4 %}_{% else %}-{% endif %}submenu'{% endif %});
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/ContentPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }
}
