<?php

namespace Kunstmaan\ArticleBundle\Controller;


use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractArticleOverviewPageController
 * @package Kunstmaan\ArticleBundle\Controller
 */
class AbstractArticleOverviewPageController extends Controller{

    /**
     * @param Request $request
     */
    public function serviceAction(Request $request)
    {

        $em = $this->get('doctrine')->getManager();
        $entity = $request->attributes->get('_entity');
        $repository = $entity->getArticleRepository($em);
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

        $request->attributes->set('_renderContext',$context);
    }
}
