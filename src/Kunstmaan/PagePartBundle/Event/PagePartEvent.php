<?php

namespace Kunstmaan\PagePartBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * PagePartEvent
 */
class PagePartEvent extends BcEvent
{
    /**
     * @var PagePartInterface
     */
    protected $pagePart;

    /**
     * @var Response
     */
    private $response = null;

    public function __construct(PagePartInterface $pagePart)
    {
        $this->pagePart = $pagePart;
    }

    /**
     * @return PagePartInterface
     */
    public function getPagePart()
    {
        return $this->pagePart;
    }

    public function setPagePart(PagePartInterface $pagePart)
    {
        $this->pagePart = $pagePart;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
