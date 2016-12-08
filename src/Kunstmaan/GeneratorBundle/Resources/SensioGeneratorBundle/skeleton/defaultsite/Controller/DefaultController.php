<?php

namespace {{ namespace }}\Controller;

use \Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    {% if multilanguage %}

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/")
     */
    public function indexAction()
    {
        return new RedirectResponse($this->generateUrl('_slug', array('url'=>'', '_locale'=>$this->container->getParameter('locale'))));
    }
    {% endif %}

}
