<?php

namespace Kunstmaan\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * The modules home controller.
 */
class ModulesController extends Controller
{
    /**
     * @Route("/", name="KunstmaanAdminBundle_modules")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        return array();
    }
}
