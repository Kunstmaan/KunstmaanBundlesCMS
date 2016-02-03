<?php

namespace Kunstmaan\NodeSearchBundle\Controller;

use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
            $search     = $this->container->get('kunstmaan_node_search.search.service');
            $pagerfanta = $search->search();

            /** @var RenderContext $renderContext */
            $renderContext               = $search->getRenderContect();
            $renderContext['pagerfanta'] = $pagerfanta;

            $request->attributes->set('_renderContext', $renderContext);
        }
    }
}
