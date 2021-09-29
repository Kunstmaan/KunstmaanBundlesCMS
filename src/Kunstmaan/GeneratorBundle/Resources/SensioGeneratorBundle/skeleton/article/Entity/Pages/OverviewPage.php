<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\{{ entity_class }}OverviewPageAdminType;
use {{ namespace }}\ViewDataProvider\{{ entity_class }}PageViewDataProvider;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractArticleOverviewPage;
use Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

/**
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}OverviewPageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_overview_pages")
 */
class {{ entity_class }}OverviewPage extends AbstractArticleOverviewPage implements HasPageTemplateInterface, SearchTypeInterface, CustomViewDataProviderInterface
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
     * @return \Doctrine\Persistence\ObjectRepository
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

    public function getViewDataProviderServiceId(): string
    {
        return {{ entity_class }}PageViewDataProvider::class;
    }
}
