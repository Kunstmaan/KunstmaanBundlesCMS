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
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use FOS\UserBundle\Util\UserManipulator;

class SettingsController extends Controller
{
    /**
     * @Route   ("/", name="KunstmaanAdminBundle_settings")
     * @Template()
     */
    public function indexAction()
    {
        /* @var EntityManager */
        $em = $this->getDoctrine()->getEntityManager();
        /* @var Request */
        $request = $this->getRequest();
        /* @var AdminList */
        $adminlist = $this->get("adminlist.factory")->createList(new UserAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'useradminlist' => $adminlist
        );
    }

    /**
     * @Route   ("/users", name="KunstmaanAdminBundle_settings_users")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function usersAction()
    {
        /* @var EntityManager */
        $em        = $this->getDoctrine()->getEntityManager();
        /* @var Request */
        $request   = $this->getRequest();
        /* @var AdminList */
        $adminlist = $this->get("adminlist.factory")->createList(new UserAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route   ("/users/add", name="KunstmaanAdminBundle_settings_users_add")
     * @Method  ({"GET", "POST"})
     * @Template()
     */
    public function adduserAction()
    {
        /* @var EntityManager */
        $em = $this->getDoctrine()->getEntityManager();
        /* @var Request */
        $request = $this->getRequest();
        $helper  = new User();
        $form    = $this->createForm(new UserType($this->container), $helper, array('password_required' => true));

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();

                /* @var UserManipulator */
                $manipulator = $this->get('fos_user.util.user_manipulator');
                $manipulator->changePassword($helper->getUsername(), $helper->getPlainpassword());

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route   ("/users/{user_id}/edit", requirements={"user_id" = "\d+"}, name="KunstmaanAdminBundle_settings_users_edit")
     * @Method  ({"GET", "POST"})
     * @Template()
     */
    public function edituserAction($user_id)
    {
        /* @var EntityManager */
        $em = $this->getDoctrine()->getEntityManager();
        /* @var Request */
        $request = $this->getRequest();
        /* @var User */
        $helper  = $em->getRepository('KunstmaanAdminBundle:User')->find();

            getUser($user_id, $em);
        $form    = $this->createForm(new UserType($this->container), $helper, array('password_required' => false));

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($helper->getPlainpassword() != "") {
                    $manipulator = $this->get('fos_user.util.user_manipulator');
                    $manipulator->changePassword($helper->getUsername(), $helper->getPlainpassword());
                }
                $helper->setPlainpassword("");
                $em->persist($helper);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
            }
        }

        return array(
            'form' => $form->createView(),
            'user' => $helper
        );
    }

    /**
     * @Route ("/users/{user_id}/delete", requirements={"user_id" = "\d+"}, name="KunstmaanAdminBundle_settings_users_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteuserAction($user_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $repo = $em->getRepository('KunstmaanAdminBundle:User');
        $item = $repo->find($user_id);
        if (!is_null($item)) {
            $em->remove($item);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
    }

    /**
     * @Route   ("/groups", name="KunstmaanAdminBundle_settings_groups")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function groupsAction()
    {
        $em        = $this->getDoctrine()->getEntityManager();
        $request   = $this->getRequest();
        $adminlist = $this->get("adminlist.factory")->createList(new GroupAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route   ("/groups/add", name="KunstmaanAdminBundle_settings_groups_add")
     * @Method  ({"GET", "POST"})
     * @Template()
     */
    public function addgroupAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $request = $this->getRequest();
        $helper  = new Group();
        $form    = $this->createForm(new GroupType($this->container), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route   ("/groups/{group_id}/edit", requirements={"group_id" = "\d+"}, name="KunstmaanAdminBundle_settings_groups_edit")
     * @Method  ({"GET", "POST"})
     * @Template()
     */
    public function editgroupAction($group_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $request = $this->getRequest();
        $helper  = $em->getRepository('KunstmaanAdminBundle:Group')->find($group_id);
        $form    = $this->createForm(new GroupType($this->container), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
            }
        }

        return array(
            'form'  => $form->createView(),
            'group' => $helper
        );
    }

    /**
     * @Route   ("/groups/{group_id}/delete", requirements={"group_id" = "\d+"}, name="KunstmaanAdminBundle_settings_groups_delete")
     * @Method  ({"GET", "POST"})
     * @Template()
     */
    public function deletegroupAction($group_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $repo = $em->getRepository('KunstmaanAdminBundle:Group');
        $item = $repo->find($group_id);
        if (!is_null($item)) {
            $em->remove($item);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
    }

    /**
     * @Route   ("/searches", name="KunstmaanAdminBundle_settings_searches")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function searchesAction()
    {
        $em        = $this->getDoctrine()->getEntityManager();
        $request   = $this->getRequest();
        $adminlist = $this->get("adminlist.factory")->createList(new SearchedForAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route   ("/logs", name="KunstmaanAdminBundle_settings_logs")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function logAction()
    {
        $em        = $this->getDoctrine()->getEntityManager();
        $request   = $this->getRequest();
        $adminlist = $this->get("adminlist.factory")->createList(new LogAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route   ("/roles", name="KunstmaanAdminBundle_settings_roles")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function rolesAction()
    {
        $em        = $this->getDoctrine()->getEntityManager();
        $request   = $this->getRequest();
        $adminlist = $this->get("adminlist.factory")->createList(new RoleAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route   ("/roles/add", name="KunstmaanAdminBundle_settings_roles_add")
     * @Method  ({"GET", "POST"})
     * @Template()
     */
    public function addroleAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $request = $this->getRequest();
        $helper  = new Role('');
        $form    = $this->createForm(new RoleType($this->container), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_roles'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route   ("/roles/{role_id}/edit", requirements={"role_id" = "\d+"}, name="KunstmaanAdminBundle_settings_roles_edit")
     * @Method  ({"GET", "POST"})
     * @Template()
     */
    public function editroleAction($role_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $request = $this->getRequest();
        $helper  = $em->getRepository('KunstmaanAdminBundle:Role')->find($role_id);
        $form    = $this->createForm(new RoleType($this->container), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_roles'));
            }
        }

        return array(
            'form' => $form->createView(),
            'role' => $helper
        );
    }

    /**
     * @Route ("/roles/{role_id}/delete", requirements={"role_id" = "\d+"}, name="KunstmaanAdminBundle_settings_roles_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteroleAction($role_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $repo = $em->getRepository('KunstmaanAdminBundle:Role');
        $item = $repo->find($role_id);
        if (!is_null($item)) {
            $em->remove($item);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_roles'));
    }

}
