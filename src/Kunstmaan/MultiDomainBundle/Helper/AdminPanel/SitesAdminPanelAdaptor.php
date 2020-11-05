<?php

namespace Kunstmaan\MultiDomainBundle\Helper\AdminPanel;

use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAction;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelActionInterface;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAdaptorInterface;

class SitesAdminPanelAdaptor implements AdminPanelAdaptorInterface
{
    /**
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions()
    {
        return [
            $this->getSiteSwitcherAction(),
        ];
    }

    private function getSiteSwitcherAction()
    {
        return new AdminPanelAction(
            [
                'path' => 'KunstmaanMultiDomainBundle_switch_site',
            ],
            '',
            '',
            '@KunstmaanMultiDomain/AdminPanel/_site_switch_action.html.twig'
        );
    }
}
