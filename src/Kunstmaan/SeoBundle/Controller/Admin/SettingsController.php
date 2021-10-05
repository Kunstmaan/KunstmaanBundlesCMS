<?php

namespace Kunstmaan\SeoBundle\Controller\Admin;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Form\RobotsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SettingsController extends AbstractController
{
    /** @var LegacyTranslatorInterface|TranslatorInterface */
    private $translator;

    public function __construct($translator)
    {
        // NEXT_MAJOR Add "Symfony\Contracts\Translation\TranslatorInterface" typehint when sf <4.4 support is removed.
        if (!$translator instanceof TranslatorInterface && !$translator instanceof LegacyTranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('The "$translator" parameter should be instance of "%s" or "%s"', TranslatorInterface::class, LegacyTranslatorInterface::class));
        }

        $this->translator = $translator;
    }

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
        $default = $this->getParameter('robots_default');
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
                $this->translator->trans('seo.robots.warning')
            );
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
