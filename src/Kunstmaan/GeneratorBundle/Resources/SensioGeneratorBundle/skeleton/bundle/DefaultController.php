<?php

namespace {{ namespace }}\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
