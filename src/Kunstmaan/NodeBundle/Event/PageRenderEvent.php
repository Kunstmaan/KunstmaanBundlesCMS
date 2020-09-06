<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PageRenderEvent extends Event
{
    /** @var Request */
    private $request;
    /** @var PageInterface */
    private $page;
    /** @var RenderContext */
    private $renderContext;
    /** @var Response|null */
    private $response;

    public function __construct(Request $request, PageInterface $page, RenderContext $renderContext)
    {
        $this->request = $request;
        $this->page = $page;
        $this->renderContext = $renderContext;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getPage(): PageInterface
    {
        return $this->page;
    }

    public function getRenderContext(): RenderContext
    {
        return $this->renderContext;
    }

    public function setRenderContext(RenderContext $renderContext): void
    {
        $this->renderContext = $renderContext;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
