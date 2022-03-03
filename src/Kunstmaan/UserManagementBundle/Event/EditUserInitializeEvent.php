<?php

declare(strict_types=1);

namespace Kunstmaan\UserManagementBundle\Event;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Event\BcEvent;
use Symfony\Component\HttpFoundation\Request;

final class EditUserInitializeEvent extends BcEvent
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
