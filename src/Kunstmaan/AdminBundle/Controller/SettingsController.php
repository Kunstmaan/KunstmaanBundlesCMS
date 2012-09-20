<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\SearchBundle\Helper\SearchedForAdminListConfigurator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\Role;
use Kunstmaan\AdminBundle\Form\UserType;
use Kunstmaan\AdminBundle\Form\GroupType;
use Kunstmaan\AdminBundle\Form\RoleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\AdminBundle\AdminList\UserAdminListConfigurator;
use Kunstmaan\AdminBundle\AdminList\GroupAdminListConfigurator;
use Kunstmaan\AdminBundle\AdminList\RoleAdminListConfigurator;
use Kunstmaan\AdminBundle\AdminList\LogAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use FOS\UserBundle\Util\UserManipulator;

/**
 * The settings controller
 *
 * @todo We should probably combine Admin & AdminList into 1 bundle, or move this controller to the AdminList bundle to prevent circular references...
 */
class SettingsController extends Controller
{
    /**
     * @Route("/", name="KunstmaanAdminBundle_settings")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList(new UserAdminListConfigurator(), $em);
        $adminList->bindRequest($request);

        return array(
            'useradminlist' => $adminList
        );
    }

    /**
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
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList(new UserAdminListConfigurator(), $em);
        $adminList->bindRequest($request);

        return array(
            'adminlist' => $adminList,
        );
    }

    /**
     * @Route("/users/add", name="KunstmaanAdminBundle_settings_users_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function addUserAction()
    {
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
     * @param int $userId
     *
     * @Route("/users/{userId}/edit", requirements={"userId" = "\d+"}, name="KunstmaanAdminBundle_settings_users_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editUserAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var User $user */
        $user = $em->getRepository('KunstmaanAdminBundle:User')->find($userId);

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
     * @param int $userId
     *
     * @Route("/users/{userId}/delete", requirements={"userId" = "\d+"}, name="KunstmaanAdminBundle_settings_users_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteUserAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var User $user */
        $user = $em->getRepository('KunstmaanAdminBundle:User')->find($userId);
        if (!is_null($user)) {
            $em->remove($user);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
    }

    /**
     * @Route("/groups", name="KunstmaanAdminBundle_settings_groups")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function groupsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new GroupAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route("/groups/add", name="KunstmaanAdminBundle_settings_groups_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function addGroupAction()
    {
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
     * @param int $groupId
     *
     * @Route("/groups/{groupId}/edit", requirements={"groupId" = "\d+"}, name="KunstmaanAdminBundle_settings_groups_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editGroupAction($groupId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var Group $group */
        $group = $em->getRepository('KunstmaanAdminBundle:Group')->find($groupId);
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
     * @param int $groupId
     *
     * @Route("/groups/{groupId}/delete", requirements={"groupId" = "\d+"}, name="KunstmaanAdminBundle_settings_groups_delete")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return RedirectResponse
     */
    public function deleteGroupAction($groupId)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('KunstmaanAdminBundle:Group')->find($groupId);
        if (!is_null($group)) {
            $em->remove($group);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
    }

    /**
     * @Route   ("/searches", name="KunstmaanAdminBundle_settings_searches")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @todo This method should be moved to KunstmaanSearchBundle
     *
     * @return array
     */
    public function searchesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new SearchedForAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route   ("/logs", name="KunstmaanAdminBundle_settings_logs")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function logAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new LogAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
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
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new RoleAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route("/roles/add", name="KunstmaanAdminBundle_settings_roles_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function addRoleAction()
    {
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
     * @param int $roleId
     *
     * @Route("/roles/{roleId}/edit", requirements={"roleId" = "\d+"}, name="KunstmaanAdminBundle_settings_roles_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editRoleAction($roleId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var Role $role */
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($roleId);
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
     * @param int $roleId
     *
     * @Route ("/roles/{roleId}/delete", requirements={"roleId" = "\d+"}, name="KunstmaanAdminBundle_settings_roles_delete")
     * @Method({"GET", "POST"})
     *
     * @return RedirectResponse
     */
    public function deleteRoleAction($roleId)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Role $role */
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($roleId);
        if (!is_null($role)) {
            $em->remove($role);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_roles'));
    }

}
