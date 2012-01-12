<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{   
	/**
	 * @Route("/", name="KunstmaanAdminBundle_homepage")
	 * @Template()
	 */
    public function indexAction()
    {
        return array();
    }   
}
