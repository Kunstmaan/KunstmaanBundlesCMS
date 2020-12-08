<?php

namespace Kunstmaan\SeoBundle\Controller\Admin;

use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Form\RobotsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends BaseSettingsController
{
    /**
     * Generates the robots administration form and fills it with a default value if needed.
     *
     * @Route(path="/", name="KunstmaanSeoBundle_settings_robots")
     * @Template(template="@KunstmaanSeo/Admin/Settings/robotsSettings.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function robotsSettingsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Robots::class);
        $robot = $repo->findOneBy([]);
        $default = $this->container->getParameter('robots_default');
        $isSaved = true;

        if (!$robot) {
            $robot = new Robots();
        }

        if ($robot->getRobotsTxt() == null) {
            $robot->setRobotsTxt($default);
            $isSaved = false;
        }

        $form = $this->createForm(RobotsType::class, $robot, [
            'action' => $this->generateUrl('KunstmaanSeoBundle_settings_robots'),
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($robot);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanSeoBundle_settings_robots'));
            }
        }

        if (!$isSaved) {
            $this->addFlash(
                FlashTypes::WARNING,
                $this->container->get('translator')->trans('seo.robots.warning')
            );
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
