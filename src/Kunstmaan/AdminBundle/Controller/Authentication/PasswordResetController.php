<?php

namespace Kunstmaan\AdminBundle\Controller\Authentication;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Form\NewPasswordType;
use Kunstmaan\AdminBundle\Form\PasswordRequestType;
use Kunstmaan\AdminBundle\Service\PasswordResetService;
use Kunstmaan\AdminBundle\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @Route("/resetting/request", name="cms_reset_password", methods={"GET", "POST"})
     * @Route("/resetting/request", name="fos_user_resetting_request", methods={"GET", "POST"})
     */
    public function resetPasswordAction(Request $request)
    {
        $form = $this->createForm(PasswordRequestType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->passwordResetService->processResetRequest($form->get('email')->getData(), $request->getLocale());

            $this->addFlash('success', 'security.resetting.send_email_success');

            return $this->redirectToRoute('cms_reset_password');
        }

        return $this->render('@KunstmaanAdmin/authentication/password_reset/request.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/confirm/{token}", name="cms_reset_password_confirm", methods={"GET", "POST"})
     */
    public function resetPasswordCheckAction(Request $request, string $token)
    {
        /** @var UserInterface|null $user */
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            $this->addFlash('danger', 'security.resetting.user_not_found');

            return $this->redirectToRoute('cms_reset_password');
        }

        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $this->passwordResetService->resetPassword($user, $form->get('plainPassword')->getData());

            $this->addFlash('success', 'security.resetting.password_set_success');

            return $response;
        }

        return $this->render('@KunstmaanAdmin/authentication/password_reset/confirm.twig', ['form' => $form->createView()]);
    }
}
