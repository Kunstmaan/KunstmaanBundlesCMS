<?php

namespace Kunstmaan\ArticleBundle\Controller;

use Kunstmaan\NodeBundle\Event\Events;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

@trigger_error(sprintf('The "%s" class is deprecated since KunstmaanArticleBundle 5.7 and will be removed in KunstmaanArticleBundle 6.0. Instead create your own event listener/subscriber for the "%s" event to customize the page render.', AbstractArticleOverviewPageController::class, Events::PAGE_RENDER), E_USER_DEPRECATED);

/**
 * @deprecated The "Kunstmaan\ArticleBundle\Controller\AbstractArticleOverviewPageController" class is deprecated since KunstmaanArticleBundle 5.7 and will be removed in KunstmaanArticleBundle 6.0. Instead create your own eventlistener for the "kunstmaan_node.page_render" event to customize the page render.
 */
class AbstractArticleOverviewPageController extends Controller
{
    /**
     * @param Request $request
     */
    public function serviceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
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

        $request->attributes->set('_renderContext', $context);
    }
}
