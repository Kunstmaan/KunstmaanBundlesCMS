<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * @final since 5.9
 */
class SlugEvent extends BcEvent
{
    /**
     * @var Response|null
     */
    protected $response;

    /**
     * @var RenderContext
     */
    protected $renderContext;

    public function __construct(?Response $response, RenderContext $renderContext)
    {
        $this->response = $response;
        $this->renderContext = $renderContext;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

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

    public function setRenderContext(RenderContext $renderContext)
    {
        $this->renderContext = $renderContext;
    }
}
