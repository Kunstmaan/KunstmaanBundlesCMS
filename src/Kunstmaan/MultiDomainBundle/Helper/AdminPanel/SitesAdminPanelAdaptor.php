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

    /**
     * @return AdminPanelLanguangeChooser[]
     */
    public function getAdminPanelLanguageChooser()
    {
        return array(
            $this->getLanguageChooserActionNext(),
        );
    }

    protected function getLanguageChooserActionNext()
    {
        return new AdminPanelAction(
            array(),
            '',
            '',
            '@KunstmaanAdmin/AdminPanel/_language_chooser_next.html.twig'
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
            '@KunstmaanMultiDomain/AdminPanel/_site_switch_action.html.twig'
        );
    }

    private function getSiteSwitcherActionNext()
    {
        return new AdminPanelAction(
            array(
                'path' => 'KunstmaanMultiDomainBundle_switch_site',
            ),
            '',
            '',
            '@KunstmaanMultiDomain/AdminPanel/_site_switch_action_next.html.twig'
        );
    }
}
