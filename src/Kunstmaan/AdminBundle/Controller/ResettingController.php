<?php

namespace Kunstmaan\AdminBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Form\NewPasswordType;
use Kunstmaan\AdminBundle\Form\PasswordRequestType;
use Kunstmaan\AdminBundle\Service\PasswordMailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResettingController extends Controller
{
    /** @var string */
    private $userClass;

    private $passwordMailer;

    public function __construct(string $userClass, PasswordMailerInterface $passwordMailer)
    {
        $this->userClass = $userClass;
        $this->passwordMailer = $passwordMailer;
    }

    /**
     * @Route("/reset_password", name="cms_reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $form = $this->createForm(PasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $token = bin2hex(random_bytes(32));
            $user = $entityManager->getRepository($this->userClass)->findOneBy(['email' => $email]);

            if ($user instanceof $this->userClass) {
                $user->setPasswordRequestToken($token);
                $entityManager->flush();
                $this->passwordMailer->sendPasswordForgotMail($user, $request->getLocale());
                $this->addFlash('success', "An email has been sent to your address");

                return $this->redirectToRoute('cms_reset_password');
            } else {
                $this->addFlash('danger', "Your email was not found");
            }
        }

        return $this->render('@KunstmaanAdmin/Security/reset-password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/confirm/{token}", name="cms_reset_password_confirm", methods={"GET", "POST"})
     */
    public function resetPasswordCheck(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $user = $entityManager->getRepository($this->userClass)->findOneBy(['passwordRequestToken' => $token]);

        if (!$token || !$user instanceof $this->userClass) {
            $this->addFlash('danger', "User not found");

            return $this->redirectToRoute('cms_reset_password');
        }

        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $password = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($password);
            $user->setPasswordRequestToken(null);
            $entityManager->flush();

            $token = new UsernamePasswordToken($user, $password, 'main');
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));

            $this->addFlash('success', "Your new password has been set");

            return $this->redirectToRoute('KunstmaanAdminBundle_homepage');
        }

        return $this->render('@KunstmaanAdmin/Security/reset-password-confirm.html.twig', ['form' => $form->createView()]);
    }
}
