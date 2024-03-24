<?php

namespace Kunstmaan\PagePartBundle\Event;

use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

final class PagePartEvent extends Event
{
    /**
     * @var PagePartInterface
     */
    protected $pagePart;

    /**
     * @var Response
     */
    private $response;

    public function __construct(PagePartInterface $pagePart)
    {
        $this->pagePart = $pagePart;
    }

    public function getPagePart(): PagePartInterface
    {
        return $this->pagePart;
    }

    public function setPagePart(PagePartInterface $pagePart)
    {
        $this->pagePart = $pagePart;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
