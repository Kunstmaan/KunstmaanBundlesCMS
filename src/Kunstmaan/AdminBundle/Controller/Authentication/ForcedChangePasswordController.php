<?php

namespace Kunstmaan\AdminBundle\Controller\Authentication;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Form\Authentication\ForcedChangePasswordForm;
use Kunstmaan\AdminBundle\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ForcedChangePasswordController extends AbstractController
{
    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    #[Route(path: '/change-password', name: 'kunstmaan_admin_forced_change_password', methods: ['GET', 'POST'])]
    public function formAction(Request $request): Response
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createForm(ForcedChangePasswordForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updatePassword($user);

            return $this->redirectToRoute('kunstmaan_admin_forced_change_password_success');
        }

        return $this->render('@KunstmaanAdmin/authentication/forced_change_password/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/change-password/success', name: 'kunstmaan_admin_forced_change_password_success', methods: ['GET'])]
    public function successAction(): Response
    {
        return $this->render('@KunstmaanAdmin/authentication/forced_change_password/success.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
