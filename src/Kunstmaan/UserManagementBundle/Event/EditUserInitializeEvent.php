<?php

declare(strict_types=1);

namespace Kunstmaan\UserManagementBundle\Event;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class EditUserInitializeEvent extends Event
{
    /** @var Request */
    private $request;

    /** @var UserInterface */
    private $user;

    public function __construct(UserInterface $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
