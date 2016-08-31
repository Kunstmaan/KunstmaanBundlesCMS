<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

interface AdminPanelAdaptorInterface
{
    /**
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions();
}
