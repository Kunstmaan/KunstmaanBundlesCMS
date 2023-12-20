<?php

namespace Kunstmaan\AdminBundle\Controller\Authentication;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\Authentication\NewPasswordType;
use Kunstmaan\AdminBundle\Form\Authentication\PasswordRequestType;
use Kunstmaan\AdminBundle\Service\PasswordResetService;
use Kunstmaan\AdminBundle\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final class PasswordResetController extends AbstractController
{
    /** @var PasswordResetService */
    private $passwordResetService;
    /** @var UserManager */
    private $userManager;

    public function __construct(PasswordResetService $passwordResetService, UserManager $userManager)
    {
        $this->passwordResetService = $passwordResetService;
        $this->userManager = $userManager;
    }

    #[Route(path: '/password-reset/request', name: 'kunstmaan_admin_reset_password', methods: ['GET', 'POST'])]
    public function requestAction(Request $request)
    {
        $form = $this->createForm(PasswordRequestType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->passwordResetService->processResetRequest($form->get('email')->getData(), $request->getLocale());
            } catch (UserNotFoundException $e) {
                // Don't expose if the user was not found to avoid leaking info.
            }

            $this->addFlash(FlashTypes::SUCCESS, 'security.resetting.send_email_success');

            return $this->redirectToRoute('kunstmaan_admin_reset_password');
        }

        return $this->render('@KunstmaanAdmin/authentication/password_reset/request.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/password-reset/confirm/{token}', name: 'kunstmaan_admin_reset_password_confirm', methods: ['GET', 'POST'])]
    public function confirmAction(Request $request, string $token)
    {
        /** @var UserInterface|null $user */
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return $this->redirectToRoute('kunstmaan_admin_reset_password');
        }

        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $this->passwordResetService->resetPassword($user, $form->get('plainPassword')->getData());

            $this->addFlash(FlashTypes::SUCCESS, 'security.resetting.password_set_success');

            return $response;
        }

        return $this->render('@KunstmaanAdmin/authentication/password_reset/confirm.twig', ['form' => $form->createView()]);
    }
}
