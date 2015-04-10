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
     * @Route(path="/", name="KunstmaanSeoBundle_settings_robots")
     * @Template(template="@KunstmaanSeo/Admin/Settings/robotsSettings.html.twig")
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function robotsSettingsAction(Request $request)
    {
        $this->checkPermission();

        $em     = $this->getDoctrine()->getManager();
        $repo   = $this->getDoctrine()->getRepository("KunstmaanSeoBundle:Robots");

        $settings = $repo->findOneBy(array());
        if (is_null($settings)) {
            $settings = new Robots();
        }

        $form = $this->createForm(new RobotsType(), $settings);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $em->persist($settings);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Robots.txt has been saved');

                return new RedirectResponse($this->generateUrl('KunstmaanSeoBundle_settings_robots'));
            }
        }

        return array(
            'form'  => $form->createView(),
        );
    }
}