<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\ContentPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;

/**
 * ContentPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}content_pages")
 */
class ContentPage extends AbstractPage  implements HasPageTemplateInterface, SearchTypeInterface
{
{% if demosite %}
    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="menu_image_id", referencedColumnName="id")
     * })
     */
    private $menuImage;

    /**
     * @var string
     *
     * @ORM\Column(name="menu_description", type="text", nullable=true)
     */
    private $menuDescription;
{% endif %}
    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new ContentPageAdminType();
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
     * @param \Kunstmaan\MediaBundle\Entity\Media $icon
     */
    public function setMenuImage($image)
    {
	$this->menuImage = $image;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
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
        return array('{{ bundle.getName() }}:main');
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
	return array('{{ bundle.getName() }}:contentpage'{% if demosite %}, '{{ bundle.getName() }}:contentpage-with-submenu'{% endif %});
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:Pages\ContentPage:view.html.twig';
    }
}
