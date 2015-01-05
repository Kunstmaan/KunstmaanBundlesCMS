<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Controller\BaseSettingsController;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Form\GroupType;
use Kunstmaan\AdminListBundle\AdminList\AdminList;

use Kunstmaan\UserManagementBundle\AdminList\GroupAdminListConfigurator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing groups in an admin list
 */
class GroupsController extends BaseSettingsController
{
    /**
     * List groups
     *
     * @Route("/", name="KunstmaanUserManagementBundle_settings_groups")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function listAction(Request $request)
    {
        $this->checkPermission();

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
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
     * @Route("/add", name="KunstmaanUserManagementBundle_settings_groups_add")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function addAction(Request $request)
    {
        $this->checkPermission();

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $group = new Group();
        $form = $this->createForm(new GroupType(), $group);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($group);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Group \''.$group->getName().'\' has been created!');

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_groups'));
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
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_groups_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        $this->checkPermission();

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        /* @var Group $group */
        $group = $em->getRepository('KunstmaanAdminBundle:Group')->find($id);
        $form = $this->createForm(new GroupType(), $group);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($group);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Group \''.$group->getName().'\' has been edited!');

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_groups'));
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
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_groups_delete")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @throws AccessDeniedException
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->checkPermission();

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('KunstmaanAdminBundle:Group')->find($id);
        if (!is_null($group)) {
            $groupname = $group->getName();
            $em->remove($group);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Group \''.$groupname.'\' has been deleted!');
        }

        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_groups'));
    }

}
