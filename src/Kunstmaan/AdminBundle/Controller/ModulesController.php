<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * The modules home controller
 */
class ModulesController extends AbstractController
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
