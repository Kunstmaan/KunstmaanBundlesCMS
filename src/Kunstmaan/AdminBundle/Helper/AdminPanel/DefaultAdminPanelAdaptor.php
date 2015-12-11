<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

use Symfony\Component\Security\Core\SecurityContextInterface;

class DefaultAdminPanelAdaptor implements AdminPanelAdaptorInterface
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions()
    {
        return array(
            $this->getLanguageChooserAction(),
            $this->getChangePasswordAction(),
            $this->getSettingsAction(),
            $this->getLogoutAction()
        );
    }

    protected function getLanguageChooserAction()
    {
        return new AdminPanelAction(
            array(),
            '',
            '',
            'KunstmaanAdminBundle:AdminPanel:_language_chooser.html.twig'
        );
    }

    protected function getSettingsAction()
    {
        return new AdminPanelAction(
            array(
                'path' => 'KunstmaanAdminBundle_settings',
                'attrs' => array('id' => 'app__settings', 'title' => 'Settings'),
            ),
            '',
            'gear'
        );
    }

    protected function getChangePasswordAction()
    {
        $user = $this->securityContext->getToken()->getUser();

        return new AdminPanelAction(
            array(
                'path' => 'KunstmaanAdminBundle_user_change_password',
                'params' => array('id' => $user->getId())
            ),
            ucfirst($user->getUsername()),
            'user'
        );
    }

    protected function getLogoutAction()
    {
        return new AdminPanelAction(
            array(
                'path' => 'fos_user_security_logout',
                'attrs' => array('id' => 'app__logout', 'title' => 'logout'),
            ),
            '',
            'sign-out'
        );
    }
}
