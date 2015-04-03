<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticleOverviewPagePagePartAdminConfigurator;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
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
     *
     * @return void|RedirectResponse
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        parent::service($container, $request, $context);

        $em = $container->get('doctrine')->getManager();
        $repository = $this->getArticleRepository($em);
	$pages = $repository->getArticles($request->getLocale());

	$adapter = new ArrayAdapter($pages);
        $pagerfanta = new Pagerfanta($adapter);
	$pagerfanta->setMaxPerPage(5);

        $pagenumber = $request->get('page');
        if (!$pagenumber || $pagenumber < 1) {
            $pagenumber = 1;
        }
        $pagerfanta->setCurrentPage($pagenumber);
        $context['pagerfanta'] = $pagerfanta;
    }

    /**
     * Return the Article repository
     *
     * @param $em
     *
     * @return mixed
     */
    abstract public function getArticleRepository($em);

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanArticleBundle:AbstractArticleOverviewPage:view.html.twig";
    }
}
