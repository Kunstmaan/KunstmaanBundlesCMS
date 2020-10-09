<?php

namespace Kunstmaan\AdminBundle\Event;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangePasswordSuccessEvent extends BcEvent
{
    /** @var Request */
    protected $request;

    /** @var UserInterface */
    protected $user;

    /** @var Response */
    private $response;

    public function __construct(UserInterface $user, Request $request, Response $response)
    {
        $this->user = $user;
        $this->request = $request;
        $this->response = $response;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getRequest(): Request
    {
        return $this->request;
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
