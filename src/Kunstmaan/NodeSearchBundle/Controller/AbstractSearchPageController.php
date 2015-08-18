<?php

namespace Kunstmaan\NodeSearchBundle\Controller;

use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\PagerFanta\Adapter\SearcherRequestAdapter;
use Kunstmaan\NodeSearchBundle\Search\AbstractElasticaSearcher;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AbstractSearchPageController
 */
class AbstractSearchPageController extends Controller
{
    /**
     * @param Request $request
     */
    public function serviceAction(Request $request)
    {
        if ($request->query->has('query')) {
            $search = $this->container->get('kunstmaan_node_search.search.service');

            $pagerfanta            = $search->search();

            $renderContext = $search->getRenderContect();
            $renderContext['pagerfanta'] = $pagerfanta;

            $request->attributes->set('_renderContext', $renderContext);
        }
    }
}
