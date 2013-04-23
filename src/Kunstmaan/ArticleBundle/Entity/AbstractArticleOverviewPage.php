<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticleOverviewPagePagePartAdminConfigurator;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The article overview page which shows its articles
 */
abstract class AbstractArticleOverviewPage extends AbstractPage implements HasPagePartsInterface
{
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array ();
    }

    /**
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new AbstractArticleOverviewPagePagePartAdminConfigurator());
    }

    /**
     * @param ContainerInterface $container
     * @param Request            $request
     * @param RenderContext      $context
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        parent::service($container, $request, $context);
        $em = $container->get('doctrine')->getManager();
        $repository = $em->getRepository('KunstmaanArticleBundle:AbstractArticlePage');
        $context['articles'] = $repository->getArticles();
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanArticleBundle:AbstractArticleOverviewPage:view.html.twig";
    }

}
