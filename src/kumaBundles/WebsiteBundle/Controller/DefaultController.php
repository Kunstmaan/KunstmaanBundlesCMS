<?php

namespace kumaBundles\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/")
     */
    public function indexAction()
    {
        return new RedirectResponse($this->generateUrl('_slug', array('url'=>'', '_locale'=>$this->container->getParameter('locale'))));
    }
    
}
