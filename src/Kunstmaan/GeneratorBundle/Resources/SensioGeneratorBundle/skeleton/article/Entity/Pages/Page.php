<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use {{ namespace }}\Form\Pages\{{ entity_class }}PageAdminType;
use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}PageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_pages")
 * @ORM\HasLifecycleCallbacks
 */
class {{ entity_class }}Page extends AbstractArticlePage implements HasPageTemplateInterface, SearchTypeInterface, HideSidebarInNodeEditInterface
{
    //%PagePartial.php.twig%

    public function __construct()
    {
        //%constructor%
    }

    //%PagePartialFunctions.php.twig%

    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ entity_class }}PageAdminType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchType()
    {
        return '{{ entity_class }}';
    }

    /**
     * @return array
     */
    public function getPagePartAdminConfigurations()
    {
        return array('{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ entity_class|lower }}main');
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array('{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ entity_class|lower }}page');
    }

    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/{{ entity_class }}Page{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    /**
     * Before persisting this entity, check the date.
     * When no date is present, fill in current date and time.
     *
     * @ORM\PrePersist
     */
    public function _prePersist()
    {
        // Set date to now when none is set
        if ($this->date == null) {
            $this->setDate(new \DateTime());
        }
    }
}
