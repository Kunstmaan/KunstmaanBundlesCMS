<?php

namespace Kunstmaan\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ModulesController extends AbstractController
{
    /**
     * @Route("/", name="KunstmaanAdminBundle_modules")
     */
    public function indexAction(): Response
    {
        return $this->render('@KunstmaanAdmin/Modules/index.html.twig');
    }
}
