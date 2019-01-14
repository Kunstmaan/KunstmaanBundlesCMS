<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultAdminPanelAdaptor implements AdminPanelAdaptorInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions()
    {
        return array(
            $this->getLanguageChooserAction(),
            $this->getChangePasswordAction(),
            $this->getLogoutAction(),
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

    protected function getChangePasswordAction()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return new AdminPanelAction(
            array(
                'path' => 'KunstmaanUserManagementBundle_settings_users_edit',
                'params' => array('id' => $user->getId()),
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
