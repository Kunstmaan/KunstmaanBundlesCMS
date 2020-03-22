<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

@trigger_error(sprintf('The "%s" class and the related "%s" and "%s" events are deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Use the "%s" class and "%s" event instead.', SlugEvent::class, PageRenderEvent::class, Events::PRE_SLUG_ACTION, Events::POST_SLUG_ACTION, Events::PAGE_RENDER), E_USER_DEPRECATED);

/**
 * @deprecated The "Kunstmaan\NodeBundle\Event\SlugEvent" class and the related "kunstmaan_node.preSlugAction" and "kunstmaan_node.postSlugAction" events are deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Use the "Kunstmaan\NodeBundle\Event\PageRenderEvent" class and "kunstmaan_node.page_render" event instead.
 */
class SlugEvent extends Event
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var RenderContext
     */
    protected $renderContext;

    /**
     * @param Response      $response
     * @param RenderContext $renderContext
     */
    public function __construct(Response $response = null, RenderContext $renderContext)
    {
        $this->response = $response;
        $this->renderContext = $renderContext;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return RenderContext
     */
    public function getRenderContext()
    {
        return $this->renderContext;
    }

    /**
     * @param RenderContext $renderContext
     */
    public function setRenderContext(RenderContext $renderContext)
    {
        $this->renderContext = $renderContext;
    }
}
