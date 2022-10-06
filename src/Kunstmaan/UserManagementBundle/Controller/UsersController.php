<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\RoleDependentUserFormInterface;
use Kunstmaan\AdminBundle\Service\UserManager;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\UserManagementBundle\Event\AfterUserDeleteEvent;
use Kunstmaan\UserManagementBundle\Event\DeleteUserInitializeEvent;
use Kunstmaan\UserManagementBundle\Event\EditUserInitializeEvent;
use Kunstmaan\UserManagementBundle\Event\UserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing users in an admin list
 */
final class UsersController extends AbstractController
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var AdminListFactory */
    private $adminListFactory;
    /** @var ParameterBagInterface */
    private $parameterBag;
    /** @var UserManager */
    private $userManager;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        TranslatorInterface $translator,
        AdminListFactory $adminListFactory,
        ParameterBagInterface $parameterBag,
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $em
    ) {
        $this->translator = $translator;
        $this->adminListFactory = $adminListFactory;
        $this->parameterBag = $parameterBag;
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
    }

    /**
     * @Route("/", name="KunstmaanUserManagementBundle_settings_users")
     */
    public function listAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $configuratorClassName = '';
        if ($this->parameterBag->has('kunstmaan_user_management.user_admin_list_configurator.class')) {
            $configuratorClassName = $this->getParameter('kunstmaan_user_management.user_admin_list_configurator.class');
        }

        $configurator = new $configuratorClassName($this->em);

        $adminList = $this->adminListFactory->createList($configurator);
        $adminList->bindRequest($request);

        return $this->render('@KunstmaanAdminList/Default/list.html.twig', [
            'adminlist' => $adminList,
        ]);
    }

    /**
     * Get an instance of the admin user class.
     *
     * @return BaseUser
     */
    private function getUserClassInstance()
    {
        $userClassName = $this->getParameter('kunstmaan_admin.user_class');

        return new $userClassName();
    }

    /**
     * @Route("/add", name="KunstmaanUserManagementBundle_settings_users_add", methods={"GET", "POST"})
     */
    public function addAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->getUserClassInstance();

        $options = ['password_required' => true, 'langs' => $this->getParameter('kunstmaan_admin.admin_locales'), 'validation_groups' => ['Registration'], 'data_class' => \get_class($user)];
        $formTypeClassName = $user->getFormTypeClass();
        $formType = new $formTypeClassName();

        if ($formType instanceof RoleDependentUserFormInterface) {
            // to edit groups and enabled the current user should have ROLE_SUPER_ADMIN
            $options['can_edit_all_fields'] = $this->isGranted('ROLE_SUPER_ADMIN');
        }

        $form = $this->createForm(
            $formTypeClassName,
            $user,
            $options
        );

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPasswordChanged(true);
                $user->setCreatedBy(method_exists($this->getUser(), 'getUserIdentifier') ? $this->getUser()->getUserIdentifier() : $this->getUser()->getUsername());
                $this->userManager->updateUser($user, true);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('kuma_user.users.add.flash.success.%username%', [
                        '%username%' => method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername(),
                    ])
                );

                return $this->redirectToRoute('KunstmaanUserManagementBundle_settings_users');
            }
        }

        return $this->render('@KunstmaanUserManagement/Users/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_edit", methods={"GET", "POST"})
     *
     * @throws AccessDeniedException
     */
    public function editAction(Request $request, $id): Response
    {
        // The logged in user should be able to change his own password/username/email and not for other users
        if ($id == $this->getUser()->getId()) {
            $requiredRole = 'ROLE_ADMIN';
        } else {
            $requiredRole = 'ROLE_SUPER_ADMIN';
        }
        $this->denyAccessUnlessGranted($requiredRole);

        /** @var UserInterface $user */
        $user = $this->em->getRepository($this->getParameter('kunstmaan_admin.user_class'))->find($id);
        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User with ID %s not found', $id));
        }

        $this->eventDispatcher->dispatch(new EditUserInitializeEvent($user, $request), UserEvents::USER_EDIT_INITIALIZE);

        $options = ['password_required' => false, 'langs' => $this->getParameter('kunstmaan_admin.admin_locales'), 'data_class' => \get_class($user)];
        $formFqn = $user->getFormTypeClass();
        $formType = new $formFqn();

        if ($formType instanceof RoleDependentUserFormInterface) {
            // to edit groups and enabled the current user should have ROLE_SUPER_ADMIN
            $options['can_edit_all_fields'] = $this->isGranted('ROLE_SUPER_ADMIN');
        }

        $event = new AdaptSimpleFormEvent($request, $formFqn, $user, $options);
        $event = $this->eventDispatcher->dispatch($event, Events::ADAPT_SIMPLE_FORM);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formFqn, $user, $options);

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->handleRequest($request);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $this->userManager->updateUser($user, true);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('kuma_user.users.edit.flash.success.%username%', [
                        '%username%' => method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername(),
                    ])
                );

                return $this->redirectToRoute('KunstmaanUserManagementBundle_settings_users_edit', ['id' => $id]);
            }
        }

        $params = [
            'form' => $form->createView(),
            'user' => $user,
        ];

        if ($tabPane) {
            $params = array_merge($params, ['tabPane' => $tabPane]);
        }

        return $this->render('@KunstmaanUserManagement/Users/edit.html.twig', $params);
    }

    /**
     * @Route("/form-delete/{id}", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_form_delete", methods={"POST"})
     */
    public function deleteFormAction(Request $request, $id): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete-user', $submittedToken)) {
            return $this->redirectToRoute('KunstmaanUserManagementBundle_settings_users');
        }

        /* @var UserInterface $user */
        $user = $this->em->getRepository($this->getParameter('kunstmaan_admin.user_class'))->find($id);
        if (!\is_null($user)) {
            $this->eventDispatcher->dispatch(new DeleteUserInitializeEvent($user, $request), UserEvents::USER_DELETE_INITIALIZE);

            $deletedUser = method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername();
            $deletedBy = method_exists($this->getUser(), 'getUserIdentifier') ? $this->getUser()->getUserIdentifier() : $this->getUser()->getUsername();

            $afterDeleteEvent = new AfterUserDeleteEvent($deletedUser, $deletedBy);

            $this->em->remove($user);
            $this->em->flush();

            $this->eventDispatcher->dispatch($afterDeleteEvent, UserEvents::AFTER_USER_DELETE);

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->translator->trans('kuma_user.users.delete.flash.success.%username%', [
                    '%username%' => method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername(),
                ])
            );
        }

        return $this->redirectToRoute('KunstmaanUserManagementBundle_settings_users');
    }

    public function changePasswordAction(): RedirectResponse
    {
        // Redirect to current user edit route...
        return $this->redirectToRoute('KunstmaanUserManagementBundle_settings_users_edit', ['id' => $this->getUser()->getId()]);
    }
}
