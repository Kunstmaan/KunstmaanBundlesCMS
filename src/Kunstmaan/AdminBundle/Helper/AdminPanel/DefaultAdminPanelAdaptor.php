<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class DefaultAdminPanelAdaptor implements AdminPanelAdaptorInterface
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;
    /** @var LogoutUrlGenerator */
    private $logoutUrlGenerator;

    public function __construct(TokenStorageInterface $tokenStorage, ?LogoutUrlGenerator $logoutUrlGenerator)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logoutUrlGenerator = $logoutUrlGenerator;
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
            ucfirst(method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername()),
            'user'
        );
    }

    protected function getLogoutAction()
    {
        return new AdminPanelLogoutAction(
            $this->logoutUrlGenerator->getLogoutUrl(),
            '',
            'sign-out'
        );
    }
}
