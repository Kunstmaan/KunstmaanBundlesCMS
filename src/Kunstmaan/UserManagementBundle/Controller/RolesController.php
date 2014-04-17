<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\AdminBundle\Entity\Role;
use Kunstmaan\AdminBundle\Form\RoleType;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\UserManagementBundle\AdminList\RoleAdminListConfigurator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing roles in an admin list
 */
class RolesController extends BaseSettingsController
{
    /**
     * List roles
     *
     * @Route   ("/", name="KunstmaanUserManagementBundle_settings_roles")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function rolesAction()
    {
        $this->checkPermission();

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
     * @Route("/add", name="KunstmaanUserManagementBundle_settings_roles_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array|RedirectResponse
     */
    public function addRoleAction()
    {
        $this->checkPermission();

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

                $this->get('session')->getFlashBag()->add('success', 'Role \''.$role->getRole().'\' has been created!');

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_roles'));
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
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_roles_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array|RedirectResponse
     */
    public function editRoleAction($id)
    {
        $this->checkPermission();

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

                $this->get('session')->getFlashBag()->add('success', 'Role \''.$role->getRole().'\' has been edited!');

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_roles'));
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
     * @Route ("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_roles_delete")
     * @Method({"GET", "POST"})
     *
     * @throws AccessDeniedException
     * @return RedirectResponse
     */
    public function deleteRoleAction($id)
    {
        $this->checkPermission();

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var Role $role */
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($id);
        if (!is_null($role)) {
            $rolename = $role->getRole();
            $em->remove($role);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Role \''.$rolename.'\' has been deleted!');
        }

        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_roles'));
    }

}