<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\{{ entity_class }}OverviewPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\ArticleBundle\Entity\AbstractArticleOverviewPage;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

/**
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}OverviewPageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_overview_pages")
 */
class {{ entity_class }}OverviewPage extends AbstractArticleOverviewPage implements HasPageTemplateInterface, SearchTypeInterface, SlugActionInterface
{
    public function getPagePartAdminConfigurations(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ entity_class|lower }}main'];
    }

    public function getPageTemplates(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ entity_class|lower }}overviewpage'];
    }

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getArticleRepository($em)
    {
        return $em->getRepository('{{ bundle.getName() }}:Pages\{{ entity_class }}Page');
    }

    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/{{ entity_class }}OverviewPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    public function getSearchType(): string
    {
        return '{{ entity_class }}';
    }

    public function getDefaultAdminType(): string
    {
        return {{ entity_class }}OverviewPageAdminType::class;
    }

    public function getControllerAction(): string
    {
        {% if isV4 %}
        return 'App\Controller\{{ entity_class }}ArticleController::serviceAction';
        {% else %}
        return '{{ bundle.getName() }}:{{ entity_class }}Article:service';
        {% endif %}
    }
}
