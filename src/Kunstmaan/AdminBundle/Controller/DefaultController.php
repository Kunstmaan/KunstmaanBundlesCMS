<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * The backend homepage
 */
class DefaultController extends Controller
{

    /**
     * @Route("/", name="KunstmaanAdminBundle_homepage")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        return array();
    }
}
