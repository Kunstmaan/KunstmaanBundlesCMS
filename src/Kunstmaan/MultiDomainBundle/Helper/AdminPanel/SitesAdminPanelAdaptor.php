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
        return array(
            $this->getSiteSwitcherAction(),
        );
    }

    private function getSiteSwitcherAction()
    {
        return new AdminPanelAction(
            array(
                'path' => 'KunstmaanMultiDomainBundle_switch_site',
            ),
            '',
            '',
            'KunstmaanMultiDomainBundle:AdminPanel:_site_switch_action.html.twig'
        );
    }
}
