<?php

namespace Kunstmaan\AdminBundle\Controller\Authentication;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class SecurityController
{
    /** @var AuthenticationUtils */
    private $authenticationUtils;
    /** @var Environment */
    private $twig;

    public function __construct(AuthenticationUtils $authenticationUtils, Environment $twig)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->twig = $twig;
    }

    #[Route(path: '/login', name: 'kunstmaan_admin_login', methods: ['GET', 'POST'])]
    public function loginAction()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return new Response($this->twig->render('@KunstmaanAdmin/authentication/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]));
    }

    #[Route(path: '/logout', name: 'kunstmaan_admin_logout')]
    public function logoutAction()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
