<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class DefaultAdminPanelAdaptor implements AdminPanelAdaptorInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;
    /** @var LogoutUrlGenerator|null */
    private $logoutUrlGenerator;

    public function __construct(TokenStorageInterface $tokenStorage, ?LogoutUrlGenerator $logoutUrlGenerator = null)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logoutUrlGenerator = $logoutUrlGenerator;

        if (null === $logoutUrlGenerator) {
            trigger_deprecation('kunstmaan/admin-bundle', '6.2', 'Not passing a value for "$logoutUrlGenerator" in "%s" is deprecated and will be required in 7.0.', __METHOD__);
        }
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
        // NEXT_MAJOR remove check
        if (null === $this->logoutUrlGenerator) {
            return new AdminPanelAction(
                [
                    'path' => 'kunstmaan_admin_logout',
                    'attrs' => ['id' => 'app__logout', 'title' => 'logout'],
                ],
                '',
                'sign-out'
            );
        }

        return new AdminPanelLogoutAction(
            $this->logoutUrlGenerator->getLogoutUrl(),
            '',
            'sign-out'
        );
    }
}
