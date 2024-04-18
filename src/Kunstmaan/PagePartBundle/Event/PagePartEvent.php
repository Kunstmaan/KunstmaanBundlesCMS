<?php

namespace Kunstmaan\PagePartBundle\Event;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
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

    private ?HasPagePartsInterface $page;

    public function __construct(PagePartInterface $pagePart, ?HasPagePartsInterface $page = null)
    {
        $this->pagePart = $pagePart;
        $this->page = $page;

        if ($page === null) {
            trigger_deprecation('kunstmaan/pagepart-bundle', '7.2', 'Not passing a HasPagePartsInterface as second parameter is deprecated and will be required in 8.0.');
        }
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

    public function getPage(): ?HasPagePartsInterface
    {
        return $this->page;
    }
}
