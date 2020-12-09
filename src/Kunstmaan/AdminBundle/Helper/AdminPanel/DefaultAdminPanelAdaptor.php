<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultAdminPanelAdaptor implements AdminPanelAdaptorInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions()
    {
        return [
            $this->getLanguageChooserAction(),
            $this->getChangePasswordAction(),
            $this->getLogoutAction(),
        ];
    }

    protected function getLanguageChooserAction()
    {
        return new AdminPanelAction(
            [],
            '',
            '',
            '@KunstmaanAdmin/AdminPanel/_language_chooser.html.twig'
        );
    }

    protected function getChangePasswordAction()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return new AdminPanelAction(
            [
                'path' => 'KunstmaanUserManagementBundle_settings_users_edit',
                'params' => ['id' => $user->getId()],
            ],
            ucfirst($user->getUsername()),
            'user'
        );
    }

    protected function getLogoutAction()
    {
        return new AdminPanelAction(
            [
                'path' => 'fos_user_security_logout',
                'attrs' => ['id' => 'app__logout', 'title' => 'logout'],
            ],
            '',
            'sign-out'
        );
    }
}
