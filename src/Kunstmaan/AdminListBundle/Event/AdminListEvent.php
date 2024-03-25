<?php

namespace Kunstmaan\AdminListBundle\Event;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

final class AdminListEvent extends Event
{
    /**
     * @var object
     */
    protected $entity;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response|null
     */
    protected $response;

    /**
     * @param object $entity
     */
    public function __construct($entity, Request $request, ?FormInterface $form = null)
    {
        $this->entity = $entity;
        $this->request = $request;
        $this->form = $form;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation.
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }
}
