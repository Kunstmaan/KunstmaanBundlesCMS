<?php

namespace Kunstmaan\SeoBundle\Controller\Admin;

use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Form\RobotsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class SettingsController extends BaseSettingsController
{
    /**
     * Generates the robots administration form and fills it with a default value if needed.
     *
     * @Route(path="/", name="KunstmaanSeoBundle_settings_robots")
     * @Template(template="@KunstmaanSeo/Admin/Settings/robotsSettings.html.twig")
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function robotsSettingsAction(Request $request)
    {
        $this->checkPermission();

        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository("KunstmaanSeoBundle:Robots");
        $robot = $repo->findOneBy(array());
        $default = $this->container->getParameter('robots_default');
        $isSaved = true;

        if (!$robot) {
            $robot = new Robots();
        }

        if ($robot->getRobotsTxt() == NULL) {
            $robot->setRobotsTxt($default);
            $isSaved = false;
        }

        $form = $this->createForm(new RobotsType(), $robot);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $em->persist($robot);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanSeoBundle_settings_robots'));
            }
        }

        if (!$isSaved) {
            $warning = $this->get('translator')->trans('seo.robots.warning');
            $this->get('session')->getFlashBag()->add('warning', $warning);
        }

        return array(
            'form' => $form->createView(),
        );
    }
}