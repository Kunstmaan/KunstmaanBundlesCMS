<?php

namespace Kunstmaan\AdminBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\DashboardConfigurationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The default controller is used to render the main screen the users see when they log in to the admin
 */
final class DefaultController extends AbstractController
{
    /** @var ParameterBagInterface */
    private $parameterBag;
    /** @var ManagerRegistry */
    private $managerRegistry;
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(ParameterBagInterface $parameterBag, ManagerRegistry $managerRegistry, TranslatorInterface $translator)
    {
        $this->parameterBag = $parameterBag;
        $this->managerRegistry = $managerRegistry;
        $this->translator = $translator;
    }

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     */
    #[Route(path: '/', name: 'KunstmaanAdminBundle_homepage')]
    public function indexAction(): Response
    {
        if ($this->parameterBag->has('kunstmaan_admin.dashboard_route')) {
            return $this->redirect($this->generateUrl($this->getParameter('kunstmaan_admin.dashboard_route')));
        }

        /* @var DashboardConfiguration $dashboardConfiguration */
        $dashboardConfiguration = $this->managerRegistry
            ->getManager()
            ->getRepository(DashboardConfiguration::class)
            ->findOneBy([]);

        return $this->render('@KunstmaanAdmin/Default/index.html.twig', ['dashboardConfiguration' => $dashboardConfiguration]);
    }

    /**
     * The admin of the index page
     */
    #[Route(path: '/adminindex', name: 'KunstmaanAdminBundle_homepage_admin')]
    public function editIndexAction(Request $request): Response
    {
        $em = $this->managerRegistry->getManager();

        /* @var DashboardConfiguration $dashboardConfiguration */
        $dashboardConfiguration = $em
            ->getRepository(DashboardConfiguration::class)
            ->findOneBy([]);

        if (\is_null($dashboardConfiguration)) {
            $dashboardConfiguration = new DashboardConfiguration();
        }
        $form = $this->createForm(DashboardConfigurationType::class, $dashboardConfiguration);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($dashboardConfiguration);
                $em->flush();

                $this->addFlash(FlashTypes::SUCCESS, $this->translator->trans('kuma_admin.edit.flash.success'));

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_homepage'));
            }
        }

        return $this->render('@KunstmaanAdmin/Default/editIndex.html.twig', [
            'form' => $form->createView(),
            'dashboardConfiguration' => $dashboardConfiguration,
        ]);
    }
}
