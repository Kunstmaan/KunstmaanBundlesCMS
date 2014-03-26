<?php

namespace Kunstmaan\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Util\UserManipulator;
use Kunstmaan\AdminBundle\Form\RoleDependentUserFormInterface;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing users in an admin list
 */
class UsersController extends BaseSettingsController
{
    /**
     * List users
     *
     * @Route("/", name="KunstmaanAdminBundle_settings_users")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function usersAction()
    {
        $this->checkPermission();

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $userObject= $this->getUserClassInstance();
        $configuratorClassName = $userObject->getAdminListConfiguratorClass();
        $configurator = new $configuratorClassName($em);

        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList($configurator);
        $adminList->bindRequest($request);

        return array(
            'adminlist' => $adminList,
        );
    }

    /**
     * Add a user
     *
     * @Route("/add", name="KunstmaanAdminBundle_settings_users_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function addUserAction()
    {
        $this->checkPermission();

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $user = $this->getUserClassInstance();
        $formTypeClassName = $user->getFormTypeClass();
        $formType = new $formTypeClassName();

        if ($formType instanceof RoleDependentUserFormInterface) {
            // to edit groups and enabled the current user should have ROLE_SUPER_ADMIN
            $formType->setCanEditAllFields($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN'));
        }

        $form = $this->createForm($formType, $user, array('password_required' => true, 'validation_groups' => array('Registration')));

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();

                /* @var UserManipulator $manipulator */
                $manipulator = $this->get('fos_user.util.user_manipulator');
                $manipulator->changePassword($user->getUsername(), $user->getPlainpassword());

                $this->get('session')->getFlashBag()->add('success', 'User \''.$user->getUsername().'\' has been created!');

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
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
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_users_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function editUserAction($id)
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
        $request = $this->getRequest();

        $user = $em->getRepository($this->container->getParameter('fos_user.model.user.class'))->find($id);
        $formTypeClassName = $user->getFormTypeClass();
        $formType = new $formTypeClassName();

        // create an array with all languages to pass to the form
        if ($this->container->hasParameter('requiredadminlocales')) {
            $langs = explode('|', $this->container->getParameter('requiredadminlocales'));
            $langs = array_combine($langs, $langs);
            array_unshift($langs, '');
        } else if ($this->container->hasParameter('defaultadminlocale')) {
            $langs = [$this->container->getParameter('defaultadminlocale') => $this->container->getParameter('defaultadminlocale')];
        } else {
            $langs = explode('|', $this->container->getParameter('requiredlocales'));
            $langs = array_combine($langs, $langs);
            array_unshift($langs, '');
        }
        $formType->setLangs($langs);

        if ($formType instanceof RoleDependentUserFormInterface) {
            // to edit groups and enabled the current user should have ROLE_SUPER_ADMIN
            $formType->setCanEditAllFields($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN'));
        }

        $form = $this->createForm($formType, $user, array('password_required' => false));

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($user->getPlainpassword() != "") {
                    $manipulator = $this->get('fos_user.util.user_manipulator');
                    $manipulator->changePassword($user->getUsername(), $user->getPlainpassword());
                }
                $user->setPlainpassword("");
                $em->persist($user);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'User \''.$user->getUsername().'\' has been edited!');

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users_edit', array('id' => $id)));
            }
        }

        return array(
            'form' => $form->createView(),
            'user' => $user
        );
    }

    /**
     * Delete a user
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_users_delete")
     * @Method({"GET", "POST"})
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function deleteUserAction($id)
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
            $this->get('session')->getFlashBag()->add('success', 'User \''.$username.'\' has been deleted!');
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
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
}
