<?php

namespace Kunstmaan\NodeBundle\Event;


use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class SlugEvent
 * @package Kunstmaan\NodeBundle\Event
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
     * @param Response $response
     * @param RenderContext $renderContext
     */
    function __construct(Response $response = null, RenderContext $renderContext)
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