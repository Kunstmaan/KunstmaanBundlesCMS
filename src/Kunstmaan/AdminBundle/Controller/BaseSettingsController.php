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
    protected function checkPermission()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }
    }

} 