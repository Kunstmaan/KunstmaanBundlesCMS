<?php

namespace Kunstmaan\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\Role;
use Kunstmaan\AdminBundle\Form\UserType;
use Kunstmaan\AdminBundle\Form\GroupType;
use Kunstmaan\AdminBundle\Form\RoleType;
use Kunstmaan\AdminBundle\AdminList\UserAdminListConfigurator;
use Kunstmaan\AdminBundle\AdminList\GroupAdminListConfigurator;
use Kunstmaan\AdminBundle\AdminList\RoleAdminListConfigurator;
use Kunstmaan\AdminBundle\AdminList\LogAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminList;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use FOS\UserBundle\Util\UserManipulator;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing the users, roles and
 * groups in an admin list
 */
class SettingsController extends Controller
{
    /**
     * Index page for the settings
     *
     * @Route("/", name="KunstmaanAdminBundle_settings")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * List users
     *
     * @Route("/users", name="KunstmaanAdminBundle_settings_users")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function usersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList(new UserAdminListConfigurator($em));
        $adminList->bindRequest($request);

        return array(
            'adminlist' => $adminList,
        );
    }

    /**
     * Add a user
     *
     * @Route("/users/add", name="KunstmaanAdminBundle_settings_users_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function addUserAction()
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $user = new User();
        $form = $this->createForm(new UserType(), $user, array('password_required' => true));

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();

                /* @var UserManipulator $manipulator */
                $manipulator = $this->get('fos_user.util.user_manipulator');
                $manipulator->changePassword($user->getUsername(), $user->getPlainpassword());

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
     * @Route("/users/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_users_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editUserAction($id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var User $user */
        $user = $em->getRepository('KunstmaanAdminBundle:User')->find($id);

        $form = $this->createForm(new UserType(), $user, array('password_required' => false));

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($user->getPlainpassword() != "") {
                    $manipulator = $this->get('fos_user.util.user_manipulator');
                    $manipulator->changePassword($user->getUsername(), $user->getPlainpassword());
                }
                $user->setPlainpassword("");
                $em->persist($user);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
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
     * @Route("/users/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_users_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteUserAction($id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var User $user */
        $user = $em->getRepository('KunstmaanAdminBundle:User')->find($id);
        if (!is_null($user)) {
            $em->remove($user);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
    }

    /**
     * List groups
     *
     * @Route("/groups", name="KunstmaanAdminBundle_settings_groups")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function groupsAction()
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new GroupAdminListConfigurator($em));
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * Add a group
     *
     * @Route("/groups/add", name="KunstmaanAdminBundle_settings_groups_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function addGroupAction()
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $group = new Group();
        $form = $this->createForm(new GroupType(), $group);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($group);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Edit a group
     *
     * @param int $id
     *
     * @Route("/groups/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_groups_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editGroupAction($id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var Group $group */
        $group = $em->getRepository('KunstmaanAdminBundle:Group')->find($id);
        $form = $this->createForm(new GroupType(), $group);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($group);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
            }
        }

        return array(
            'form'  => $form->createView(),
            'group' => $group
        );
    }

    /**
     * Delete a group
     *
     * @param int $id
     *
     * @Route("/groups/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_groups_delete")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return RedirectResponse
     */
    public function deleteGroupAction($id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('KunstmaanAdminBundle:Group')->find($id);
        if (!is_null($group)) {
            $em->remove($group);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
    }

    /**
     * Display logs
     *
     * @Route   ("/logs", name="KunstmaanAdminBundle_settings_logs")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function logAction()
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new LogAdminListConfigurator($em));
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * List roles
     *
     * @Route   ("/roles", name="KunstmaanAdminBundle_settings_roles")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function rolesAction()
    {
        $em        = $this->getDoctrine()->getManager();
        $request   = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new RoleAdminListConfigurator($em));
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * Add a role
     *
     * @Route("/roles/add", name="KunstmaanAdminBundle_settings_roles_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function addRoleAction()
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $role = new Role('');
        $form = $this->createForm(new RoleType(), $role);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($role);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_roles'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Edit a role
     *
     * @param int $id
     *
     * @Route("/roles/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_roles_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editRoleAction($id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var Role $role */
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($id);
        $form = $this->createForm(new RoleType(), $role);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($role);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_roles'));
            }
        }

        return array(
            'form' => $form->createView(),
            'role' => $role
        );
    }

    /**
     * Delete a role
     *
     * @param int $id
     *
     * @Route ("/roles/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_settings_roles_delete")
     * @Method({"GET", "POST"})
     *
     * @return RedirectResponse
     */
    public function deleteRoleAction($id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var Role $role */
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($id);
        if (!is_null($role)) {
            $em->remove($role);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_roles'));
    }

}
