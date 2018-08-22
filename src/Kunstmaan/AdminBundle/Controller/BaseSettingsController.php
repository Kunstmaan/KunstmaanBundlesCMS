<?php

namespace Kunstmaan\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseSettingsController extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    protected function get($id)
    {
        @trigger_error('Getting services directly from the container is deprecated in KunstmaanAdminBundle 5.1 and will be removed in KunstmaanAdminBundle 6.0. Register your controllers as services and inject the necessary dependencies.', E_USER_DEPRECATED);

        return parent::get($id);
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    protected function getParameter($name)
    {
        @trigger_error('Getting parameters directly from the container is deprecated in KunstmaanAdminBundle 5.1 and will be removed in KunstmaanAdminBundle 6.0. Register your controllers as services and inject the necessary parameters.', E_USER_DEPRECATED);

        return parent::getParameter($name);
    }
}
