<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\{{ entity_class }}OverviewPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\ArticleBundle\Entity\AbstractArticleOverviewPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;

/**
 * The article overview page which shows its articles
 *
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}OverviewPageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_overview_pages")
 */
class {{ entity_class }}OverviewPage extends AbstractArticleOverviewPage implements HasPageTemplateInterface, SearchTypeInterface, SlugActionInterface
{
    /**
     * @return AbstractPagePartAdminConfigurator[]
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
        return array('{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ entity_class|lower }}overviewpage');
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

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/{{ entity_class }}OverviewPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchType()
    {
        return '{{ entity_class }}';
    }

    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ entity_class }}OverviewPageAdminType::class;
    }

    /**
     * @return string
     *
     */
    public function getControllerAction()
    {
        {% if isV4 %}
        return 'App\Controller\{{ entity_class }}ArticleController::serviceAction';
        {% else %}
        return '{{ bundle.getName() }}:{{ entity_class }}Article:service';
        {% endif %}
    }
}
