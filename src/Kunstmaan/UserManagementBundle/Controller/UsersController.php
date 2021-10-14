<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\RoleDependentUserFormInterface;
use Kunstmaan\AdminBundle\Helper\EventdispatcherCompatibilityUtil;
use Kunstmaan\AdminBundle\Service\UserManager;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\UserManagementBundle\Event\AfterUserDeleteEvent;
use Kunstmaan\UserManagementBundle\Event\UserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as LegacyEventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function __construct(TranslatorInterface $translator, AdminListFactory $adminListFactory, ParameterBagInterface $parameterBag, UserManager $userManager, $eventDispatcher)
    {
        // NEXT_MAJOR Add "Symfony\Contracts\EventDispatcher\EventDispatcherInterface" typehint when sf <4.4 support is removed.
        if (!$eventDispatcher instanceof EventDispatcherInterface && !$eventDispatcher instanceof LegacyEventDispatcherInterface) {
            throw new \InvalidArgumentException(sprintf('The "$eventDispatcher" parameter should be instance of "%s" or "%s"', EventDispatcherInterface::class, LegacyEventDispatcherInterface::class));
        }

        $this->translator = $translator;
        $this->adminListFactory = $adminListFactory;
        $this->parameterBag = $parameterBag;
        $this->userManager = $userManager;
        $this->eventDispatcher = EventdispatcherCompatibilityUtil::upgradeEventDispatcher($eventDispatcher);
    }

    /**
     * List users
     *
     * @Route("/", name="KunstmaanUserManagementBundle_settings_users")
     * @Template("@KunstmaanAdminList/Default/list.html.twig")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $configuratorClassName = '';
        if ($this->parameterBag->has('kunstmaan_user_management.user_admin_list_configurator.class')) {
            $configuratorClassName = $this->getParameter('kunstmaan_user_management.user_admin_list_configurator.class');
        }

        $configurator = new $configuratorClassName($em);

        $adminList = $this->adminListFactory->createList($configurator);
        $adminList->bindRequest($request);

        return [
            'adminlist' => $adminList,
        ];
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
     * Add a user
     *
     * @Route("/add", name="KunstmaanUserManagementBundle_settings_users_add", methods={"GET", "POST"})
     * @Template("@KunstmaanUserManagement/Users/add.html.twig")
     *
     * @return array
     */
    public function addAction(Request $request)
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
                $user->setCreatedBy($this->getUser()->getUsername());
                $this->userManager->updateUser($user, true);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('kuma_user.users.add.flash.success.%username%', [
                        '%username%' => $user->getUsername(),
                    ])
                );

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_users'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit a user
     *
     * @param int $id
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_edit", methods={"GET", "POST"})
     * @Template("@KunstmaanUserManagement/Users/edit.html.twig")
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function editAction(Request $request, $id)
    {
        // The logged in user should be able to change his own password/username/email and not for other users
        if ($id == $this->getUser()->getId()) {
            $requiredRole = 'ROLE_ADMIN';
        } else {
            $requiredRole = 'ROLE_SUPER_ADMIN';
        }
        $this->denyAccessUnlessGranted($requiredRole);

        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var UserInterface $user */
        $user = $em->getRepository($this->getParameter('kunstmaan_admin.user_class'))->find($id);
        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User with ID %s not found', $id));
        }

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
                        '%username%' => $user->getUsername(),
                    ])
                );

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_users_edit', ['id' => $id]));
            }
        }

        $params = [
            'form' => $form->createView(),
            'user' => $user,
        ];

        if ($tabPane) {
            $params = array_merge($params, ['tabPane' => $tabPane]);
        }

        return $params;
    }

    /**
     * @Route("/form-delete/{id}", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_form_delete", methods={"POST"})
     */
    public function deleteFormAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete-user', $submittedToken)) {
            return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_users'));
        }

        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /* @var UserInterface $user */
        $user = $em->getRepository($this->getParameter('kunstmaan_admin.user_class'))->find($id);
        if (!\is_null($user)) {
            $afterDeleteEvent = new AfterUserDeleteEvent($user->getUsername(), $this->getUser()->getUsername());

            $em->remove($user);
            $em->flush();

            $this->eventDispatcher->dispatch($afterDeleteEvent, UserEvents::AFTER_USER_DELETE);

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->translator->trans('kuma_user.users.delete.flash.success.%username%', [
                    '%username%' => $user->getUsername(),
                ])
            );
        }

        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_users'));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction()
    {
        // Redirect to current user edit route...
        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_users_edit', ['id' => $this->getUser()->getId()]));
    }
}
