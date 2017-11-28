<?php

namespace Kunstmaan\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\DashboardConfigurationType;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\EntityManagerTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\TranslatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The default controller is used to render the main screen the users see when they log in to the admin
 */
class DefaultController extends AbstractController
{
    use EntityManagerTrait,
        TranslatorTrait;

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
        $dashboardConfiguration = $this->getEntityManager()
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
        $em      = $this->getEntityManager();

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
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($dashboardConfiguration);
                $em->flush($dashboardConfiguration);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->getTranslator()->trans('kuma_admin.edit.flash.success')
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
