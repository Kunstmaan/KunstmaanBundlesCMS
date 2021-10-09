<?php

namespace Kunstmaan\NodeBundle\Helper;

use Symfony\Component\HttpFoundation\Response;

/**
 * A context for rendering pages through service methods
 */
final class RenderContext extends \ArrayObject
{
    /** @var string */
    private $view;

    /** @var Response|null */
    private $response;

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function hasResponse(): bool
    {
        return null !== $this->response;
    }
}
