<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use {{ namespace }}\Form\Pages\{{ entity_class }}PageAdminType;

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

    public function getDefaultAdminType(): string
    {
        return {{ entity_class }}PageAdminType::class;
    }

    public function getSearchType(): string
    {
        return '{{ entity_class }}';
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ entity_class|lower }}main'];
    }

    public function getPageTemplates(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ entity_class|lower }}page'];
    }

    public function getDefaultView(): string
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
