<?php

namespace Kunstmaan\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BaseSettingsController extends Controller
{

    /**
     * Check permission
     *
     * @throws AccessDeniedException
     */
    protected function checkPermission($roleToCheck = 'ROLE_SUPER_ADMIN')
    {
        if (false === $this->container->get('security.context')->isGranted($roleToCheck)) {
            throw new AccessDeniedException();
        }
    }

}
