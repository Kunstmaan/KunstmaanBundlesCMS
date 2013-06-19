<?php

namespace Kunstmaan\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Kunstmaan\AdminBundle\Form\DashboardConfigurationType;

use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * The default controller is used to render the main screen the users see when they log in to the admin
 */
class DefaultController extends Controller
{

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="KunstmaanAdminBundle_homepage")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        /* @var DashboardConfiguration $dashboardConfiguration */
        $dashboardConfiguration = $this->getDoctrine()->getManager()->getRepository('KunstmaanAdminBundle:DashboardConfiguration')->findOneBy(array());

        return array('dashboardConfiguration' => $dashboardConfiguration);
    }

    /**
     * The admin of the index page
     *
     * @Route("/adminindex", name="KunstmaanAdminBundle_homepage_admin")
     * @Template()
     *
     * @return array
     */
    public function editIndexAction()
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var DashboardConfiguration $dashboardConfiguration */
        $dashboardConfiguration = $this->getDoctrine()->getManager()->getRepository('KunstmaanAdminBundle:DashboardConfiguration')->findOneBy(array());
        if (is_null($dashboardConfiguration)) {
            $dashboardConfiguration = new DashboardConfiguration();
        }

        $form = $this->createForm(new DashboardConfigurationType(), $dashboardConfiguration);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($dashboardConfiguration);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'The welcome page has been edited!');

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_homepage'));
            }
        }

        return array(
                'form' => $form->createView(),
                'dashboardConfiguration' => $dashboardConfiguration
                );
    }
}
