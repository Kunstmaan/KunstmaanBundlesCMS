<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kunstmaan\AdminBundle\Form\DashboardConfigurationType;
use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        if ($this->container->hasParameter("kunstmaan_admin.dashboard_route")) {
            return $this->redirect($this->generateUrl($this->container->getParameter("kunstmaan_admin.dashboard_route")));
        }

        /* @var DashboardConfiguration $dashboardConfiguration */
        $dashboardConfiguration = $this->getDoctrine()
            ->getManager()
            ->getRepository('KunstmaanAdminBundle:DashboardConfiguration')
            ->findOneBy(array());

        return array('dashboardConfiguration' => $dashboardConfiguration);
    }

    /**
     * The admin of the index page
     *
     * @Route("/adminindex", name="KunstmaanAdminBundle_homepage_admin")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function editIndexAction(Request $request)
    {
        /* @var $em EntityManager */
        $em      = $this->getDoctrine()->getManager();

        /* @var DashboardConfiguration $dashboardConfiguration */
        $dashboardConfiguration = $em
            ->getRepository('KunstmaanAdminBundle:DashboardConfiguration')
            ->findOneBy(array());

        if (is_null($dashboardConfiguration)) {
            $dashboardConfiguration = new DashboardConfiguration();
        }
        $form = $this->createForm(DashboardConfigurationType::class, $dashboardConfiguration);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($dashboardConfiguration);
                $em->flush($dashboardConfiguration);
                $this->get('session')->getFlashBag()->add(
                    FlashTypes::SUCCESS,
                    $this->get('translator')->trans('kuma_admin.edit.flash.success')
                );

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_homepage'));
            }
        }

        return array(
            'form'                   => $form->createView(),
            'dashboardConfiguration' => $dashboardConfiguration
        );
    }
}
