<?php

namespace Kunstmaan\AdminBundle\Event;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Response;

final class ChangePasswordSuccessEvent extends BcEvent
{
    /** @var UserInterface */
    private $user;

    /** @var Response */
    private $response;

    public function __construct(UserInterface $user, Response $response)
    {
        $this->user = $user;
        $this->response = $response;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
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
