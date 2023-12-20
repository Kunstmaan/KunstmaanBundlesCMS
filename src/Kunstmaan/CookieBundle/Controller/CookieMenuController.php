<?php

namespace Kunstmaan\CookieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CookieMenuController extends AbstractController
{
    /**
     * @return RedirectResponse
     */
    #[Route(path: '/', name: 'kunstmaancookiebundle_admin_cookies')]
    public function cookiesAction()
    {
        return $this->redirectToRoute('kunstmaancookiebundle_admin_cookietype');
    }
}
