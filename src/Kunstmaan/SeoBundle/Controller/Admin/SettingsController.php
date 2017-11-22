<?php

namespace Kunstmaan\SeoBundle\Controller\Admin;

use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Form\RobotsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsController
 * @package Kunstmaan\SeoBundle\Controller\Admin
 */
class SettingsController extends BaseSettingsController
{
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $repo;

    /** @var ObjectManager $em */
    private $em;

    /** @var bool $isSaved */
    private $isSaved;

    /**
     * SettingsController constructor.
     */
    public function __construct()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->repo = $this->getDoctrine()->getRepository("KunstmaanSeoBundle:Robots");
    }

    /**
     * Generates the robots administration form and fills it with a default value if needed.
     *
     * @Route(path="/", name="KunstmaanSeoBundle_settings_robots")
     * @Template(template="@KunstmaanSeo/Admin/Settings/robotsSettings.html.twig")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function robotsSettingsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $robot = $this->getRobot();
        $actionUrl = $this->generateUrl('KunstmaanSeoBundle_settings_robots');
        $form = $this->createForm(RobotsType::class, $robot,['action' => $actionUrl]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                return $this->saveAndRedirect($robot);
            }
        }

        if (!$this->isSaved) {
            $this->addFlash(FlashTypes::WARNING, $this->get('translator')->trans('seo.robots.warning'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Robots $robot
     *
     * @return RedirectResponse
     */
    private function saveAndRedirect(Robots $robot)
    {
        $this->em->persist($robot);
        $this->em->flush();
        $url = $this->generateUrl('KunstmaanSeoBundle_settings_robots');
        return new RedirectResponse($url);
    }

    /**
     * @return Robots
     */
    private function getRobot()
    {
        $robot = $this->repo->findOneBy([]);
        if (!$robot) {
            $robot = new Robots();
        }
        $this->isSaved = true;
        if ($robot->getRobotsTxt() === null) {
            $default = $this->getParameter('robots_default');
            $robot->setRobotsTxt($default);
            $this->isSaved = false;
        }
        return $robot;
    }
}
