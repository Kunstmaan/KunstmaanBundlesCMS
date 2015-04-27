<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Util\UserManipulator;
use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\Form\RoleDependentUserFormInterface;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        $this->checkPermission();
        $em                    = $this->getDoctrine()->getManager();
        $userObject            = $this->getUserClassInstance();
        $configuratorClassName = '';
        if ($this->container->hasParameter('kunstmaan_user_management.user_admin_list_configurator.class')) {
            $configuratorClassName = $this->container->getParameter(
                'kunstmaan_user_management.user_admin_list_configurator.class'
            );
        }
        // Fallback for backwards compatibility - will be removed in the future!
        if (empty($configuratorClassName)) {
            $configuratorClassName = $userObject->getAdminListConfiguratorClass();
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
        $userClassName = $this->container->getParameter('fos_user.model.user.class');

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
        $this->checkPermission();
        /* @var $em EntityManager */
        $em                = $this->getDoctrine()->getManager();
        $user              = $this->getUserClassInstance();
        $formTypeClassName = $user->getFormTypeClass();
        $formType          = new $formTypeClassName();
        $formType->setLangs($this->container->getParameter('kunstmaan_admin.admin_locales'));

        if ($formType instanceof RoleDependentUserFormInterface) {
            // to edit groups and enabled the current user should have ROLE_SUPER_ADMIN
            $formType->setCanEditAllFields($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN'));
        }

        $form = $this->createForm(
            $formType,
            $user,
            array(
                'password_required' => true,
                'validation_groups' => array('Registration')
            )
        );

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* @var UserManager $userManager */
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user, true);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'User \'' . $user->getUsername() . '\' has been created!'
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
        if ($id == $this->get('security.context')->getToken()->getUser()->getId()) {
            $requiredRole = 'ROLE_ADMIN';
        } else {
            $requiredRole = 'ROLE_SUPER_ADMIN';
        }
        $this->checkPermission($requiredRole);

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $user              = $em->getRepository($this->container->getParameter('fos_user.model.user.class'))->find($id);
        $formTypeClassName = $user->getFormTypeClass();
        $formType          = new $formTypeClassName();
        $formType->setLangs($this->container->getParameter('kunstmaan_admin.admin_locales'));

        if ($formType instanceof RoleDependentUserFormInterface) {
            // to edit groups and enabled the current user should have ROLE_SUPER_ADMIN
            $formType->setCanEditAllFields($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN'));
        }

        $event = new AdaptSimpleFormEvent($request, $formType, $user);
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM , $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType, $user, array('password_required' => false));

        if ($request->isMethod('POST')) {

            if($tabPane){
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->handleRequest($request);
            }

            if ($form->isValid()) {
                /* @var UserManager $userManager */
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user, true);
                
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'User \'' . $user->getUsername() . '\' has been edited!'
                );

                return new RedirectResponse($this->generateUrl(
                    'KunstmaanUserManagementBundle_settings_users_edit',
                    array('id' => $id)
                ));
            }
        }

    $params = array(
        'form' => $form->createView(),
        'user' => $user,
        );

    if($tabPane) {
        $params = array_merge($params, array('tabPane' => $tabPane));
    }

    return $params;
    }

    /**
     * Delete a user
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_users_delete")
     * @Method({"GET", "POST"})
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function deleteAction($id)
    {
        $this->checkPermission();

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var User $user */
        $user = $em->getRepository($this->container->getParameter('fos_user.model.user.class'))->find($id);
        if (!is_null($user)) {
            $username = $user->getUsername();
            $em->remove($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'User \'' . $username . '\' has been deleted!');
        }

        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_users'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction()
    {
        // Redirect to current user edit route...
        return new RedirectResponse(
            $this->generateUrl(
                'KunstmaanUserManagementBundle_settings_users_edit',
                array(
                    'id' => $this->get('security.context')->getToken()->getUser()->getId()
                )
            )
        );
    }
}
