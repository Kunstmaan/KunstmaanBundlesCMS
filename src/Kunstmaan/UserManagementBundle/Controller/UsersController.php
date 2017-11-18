<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Model\UserInterface;
use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Entity\HasApiKeyInterface;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\RoleDependentUserFormInterface;
use Kunstmaan\AdminBundle\Repository\UserRepository;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\UserManagementBundle\Event\UserEvents;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing users in an admin list
 */
class UsersController extends BaseSettingsController
{
    /**
     * List users
     *
     * @Route("/", name="KunstmaanUserManagementBundle_settings_users")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $configuratorClassName = '';
        if ($this->container->hasParameter('kunstmaan_user_management.user_admin_list_configurator.class')) {
            $configuratorClassName = $this->getParameter(
                'kunstmaan_user_management.user_admin_list_configurator.class'
            );
        }

        $configurator = new $configuratorClassName($em);

        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList($configurator);
        $adminList->bindRequest($request);

        return array(
            'adminlist' => $adminList,
        );
    }

    /**
     * Get an instance of the admin user class.
     *
     * @return BaseUser
     */
    private function getUserClassInstance()
    {
        $userClassName = $this->getParameter('fos_user.model.user.class');

        return new $userClassName();
    }

    /**
     * Add a user
     *
     * @Route("/add", name="KunstmaanUserManagementBundle_settings_users_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->getUserClassInstance();

        $options = array('password_required' => true, 'langs' => $this->getParameter('kunstmaan_admin.admin_locales'), 'validation_groups' => array('Registration'), 'data_class' => get_class($user));
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
                /* @var UserManager $userManager */
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user, true);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->get('translator')->trans('kuma_user.users.add.flash.success.%username%', [
                        '%username%' => $user->getUsername()
                    ])
                );

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_users'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Edit a user
     *
     * @param int $id
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        // The logged in user should be able to change his own password/username/email and not for other users
        if ($id == $this->get('security.token_storage')->getToken()->getUser()->getId()) {
            $requiredRole = 'ROLE_ADMIN';
        } else {
            $requiredRole = 'ROLE_SUPER_ADMIN';
        }
        $this->denyAccessUnlessGranted($requiredRole);

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        /** @var UserInterface $user */
        $user = $em->getRepository($this->getParameter('fos_user.model.user.class'))->find($id);
        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User with ID %s not found', $id));
        }

        $userEvent = new UserEvent($user, $request);
        $this->container->get('event_dispatcher')->dispatch(UserEvents::USER_EDIT_INITIALIZE, $userEvent);

        $options = array('password_required' => false, 'langs' => $this->getParameter('kunstmaan_admin.admin_locales'), 'data_class' => get_class($user));
        $formFqn = $user->getFormTypeClass();
        $formType = new $formFqn();

        if ($formType instanceof RoleDependentUserFormInterface) {
            // to edit groups and enabled the current user should have ROLE_SUPER_ADMIN
            $options['can_edit_all_fields'] = $this->isGranted('ROLE_SUPER_ADMIN');
        }

        $event = new AdaptSimpleFormEvent($request, $formFqn, $user, $options);
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
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
                /* @var UserManager $userManager */
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user, true);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->get('translator')->trans('kuma_user.users.edit.flash.success.%username%', [
                        '%username%' => $user->getUsername()
                    ])
                );

                return new RedirectResponse(
                    $this->generateUrl(
                        'KunstmaanUserManagementBundle_settings_users_edit',
                        array('id' => $id)
                    )
                );
            }
        }

        $params = array(
            'form' => $form->createView(),
            'user' => $user,
        );

        if ($tabPane) {
            $params = array_merge($params, array('tabPane' => $tabPane));
        }

        return $params;
    }

    /**
     * Delete a user
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_delete")
     * @Method({"GET", "POST"})
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var UserInterface $user */
        $user = $em->getRepository($this->getParameter('fos_user.model.user.class'))->find($id);
        if (!is_null($user)) {
            $userEvent = new UserEvent($user, $request);
            $this->container->get('event_dispatcher')->dispatch(UserEvents::USER_DELETE_INITIALIZE, $userEvent);

            $em->remove($user);
            $em->flush();

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->get('translator')->trans('kuma_user.users.delete.flash.success.%username%', [
                    '%username%' => $user->getUsername()
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
        return new RedirectResponse(
            $this->generateUrl(
                'KunstmaanUserManagementBundle_settings_users_edit',
                array(
                    'id' => $this->get('security.token_storage')->getToken()->getUser()->getId(),
                )
            )
        );
    }

    /**
     * Delete a user
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/{id}/generate-key", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_key_generate")
     * @Method({"GET"})
     *
     * @throws AccessDeniedException
     * @throws BadRequestHttpException
     * @return RedirectResponse
     */
    public function generateKeyAction(Request $request, $id)
    {
        // The logged in user should be able to change his own generated api key and not for other users
        if ($id == $this->get('security.token_storage')->getToken()->getUser()->getId()) {
            $requiredRole = 'ROLE_ADMIN';
        } else {
            $requiredRole = 'ROLE_SUPER_ADMIN';
        }
        $this->denyAccessUnlessGranted($requiredRole);

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /** @var UserRepository $repo */
        $repo = $em->getRepository($this->getParameter('fos_user.model.user.class'));
        /* @var UserInterface $user */
        $user = $repo->find($id);

        if(!$user instanceof HasApiKeyInterface) {
            throw new BadRequestHttpException('user needs to have api key implemented');
        }

        if (!is_null($user)) {
            $userEvent = new UserEvent($user, $request);
            $this->container->get('event_dispatcher')->dispatch(UserEvents::USER_KEY_GENERATE_INITIALIZE , $userEvent);

            $user->setApiKey(Uuid::uuid5(Uuid::NAMESPACE_URL, 'apikey'));
            $em->flush();

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->get('translator')->trans('kuma_user.users.key_generate.flash.success.%username%', [
                    '%username%' => $user->getUsername()
                ])
            );
        }

        return new RedirectResponse(
            $this->generateUrl(
                'KunstmaanUserManagementBundle_settings_users_edit',
                array('id' => $id)
            )
        );
    }
}
