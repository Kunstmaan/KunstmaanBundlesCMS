<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\AdminBundle\Entity\Role;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\RoleType;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\UserManagementBundle\AdminList\RoleAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em        = $this->getDoctrine()->getManager();
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
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $role = new Role('');
        $form = $this->createForm(RoleType::class, $role);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($role);
                $em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->get('translator')->trans('kuma_user.roles.add.flash.success.%role%', [
                        '%role%' => $role->getRole()
                    ])
                );

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
    public function editAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var Role $role */
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($id);
        $form = $this->createForm(RoleType::class, $role);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($role);
                $em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->get('translator')->trans('kuma_user.roles.edit.flash.success.%role%', [
                        '%role%' => $role->getRole()
                    ])
                );

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
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var Role $role */
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($id);
        if (!is_null($role)) {
            $em->remove($role);
            $em->flush();

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->get('translator')->trans('kuma_user.roles.delete.flash.success.%role%', [
                    '%role%' => $role->getRole()
                ])
            );
        }

        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_roles'));
    }

}
