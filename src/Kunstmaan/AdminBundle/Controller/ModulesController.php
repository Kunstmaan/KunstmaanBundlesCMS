<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The modules home controller
 */
class ModulesController extends Controller
{
    /**
     * @Route("/", name="KunstmaanAdminBundle_modules")
     * @Template("@KunstmaanAdmin/Modules/index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }
}
