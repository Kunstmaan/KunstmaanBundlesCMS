<?php

namespace Kunstmaan\NodeSearchBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RenderPageEvent
 *
 * @package Kunstmaan\NodeSearchBundle\Event
 */
class RenderPageEvent extends Event
{
    const EVENT_RENDER_PAGE = 'kunstmaan_node_search.onRenderPage';

    /** @var HasNodeInterface */
    private $page;

    /** @var RenderContext */
    private $renderContext;

    /** @var Request */
    private $request;

    /**
     * RenderPageEvent constructor.
     *
     * @param HasNodeInterface $page
     * @param RenderContext    $renderContext
     * @param Request          $request
     */
    public function __construct(HasNodeInterface $page, RenderContext $renderContext, Request $request)
    {
        $this->page = $page;
        $this->renderContext = $renderContext;
        $this->request = $request;
    }

    /**
     * @return HasNodeInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return RenderContext
     */
    public function getRenderContext()
    {
        return $this->renderContext;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
