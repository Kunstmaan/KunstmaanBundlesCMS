<?php

namespace Kunstmaan\AdminBundle\Event;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordSuccessEvent extends BcEvent
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var UserInterface
     */
    protected $user;
    /** @var Response */
    private $response;

    public function __construct(UserInterface $user, Request $request = null, Response $response = null)
    {
        $this->user = $user;
        $this->request = $request;
        $this->response = $response;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
