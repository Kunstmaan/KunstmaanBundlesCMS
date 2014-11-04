<?php

namespace {{ namespace }}\Entity\{{ entity_class }};

use Doctrine\ORM\Mapping as ORM;
use {{ namespace }}\Form\{{ entity_class }}\{{ entity_class }}OverviewPageAdminType;
use {{ namespace }}\PagePartAdmin\{{ entity_class }}\{{ entity_class }}OverviewPagePagePartAdminConfigurator;
use Kunstmaan\ArticleBundle\Entity\AbstractArticleOverviewPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The article overview page which shows its articles
 *
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}\{{ entity_class }}OverviewPageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_overviewpages")
 */
class {{ entity_class }}OverviewPage extends AbstractArticleOverviewPage
{

    /**
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new {{ entity_class }}OverviewPagePagePartAdminConfigurator());
    }

    /**
     * @param ContainerInterface $container
     * @param Request            $request
     * @param RenderContext      $context
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        parent::service($container, $request, $context);
    }

    public function getArticleRepository($em)
    {
        return $em->getRepository('{{ bundle.getName() }}:{{ entity_class }}\{{ entity_class }}Page');
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:{{ entity_class }}/{{ entity_class }}OverviewPage:view.html.twig';
    }

    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new {{ entity_class }}OverviewPageAdminType();
    }

}
