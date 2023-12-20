<?php

namespace Kunstmaan\SeoBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Form\RobotsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SettingsController extends AbstractController
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * Generates the robots administration form and fills it with a default value if needed.
     */
    #[Route(path: '/', name: 'KunstmaanSeoBundle_settings_robots')]
    public function robotsSettingsAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $repo = $this->em->getRepository(Robots::class);
        $robot = $repo->findOneBy([]);
        $default = $this->getParameter('robots_default');
        $isSaved = true;

        if (!$robot) {
            $robot = new Robots();
        }

        if ($robot->getRobotsTxt() == null) {
            $robot->setRobotsTxt($default);
            $isSaved = false;
        }

        $form = $this->createForm(RobotsType::class, $robot, ['action' => $this->generateUrl('KunstmaanSeoBundle_settings_robots')]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($robot);
                $this->em->flush();

                return $this->redirectToRoute('KunstmaanSeoBundle_settings_robots');
            }
        }

        if (!$isSaved) {
            $this->addFlash(
                FlashTypes::WARNING,
                $this->translator->trans('seo.robots.warning')
            );
        }

        return $this->render('@KunstmaanSeo/Admin/Settings/robotsSettings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
