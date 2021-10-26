<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\GroupType;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\UserManagementBundle\AdminList\GroupAdminListConfigurator;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing groups in an admin list
 */
final class GroupsController extends AbstractController
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var AdminListFactory */
    private $adminListFactory;

    public function __construct(TranslatorInterface $translator, AdminListFactory $adminListFactory)
    {
        $this->translator = $translator;
        $this->adminListFactory = $adminListFactory;
    }

    /**
     * List groups
     *
     * @Route("/", name="KunstmaanUserManagementBundle_settings_groups")
     * @Template("@KunstmaanAdminList/Default/list.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $adminlist = $this->adminListFactory->createList(new GroupAdminListConfigurator($em));
        $adminlist->bindRequest($request);

        return [
            'adminlist' => $adminlist,
        ];
    }

    /**
     * Add a group
     *
     * @Route("/add", name="KunstmaanUserManagementBundle_settings_groups_add", methods={"GET", "POST"})
     * @Template("@KunstmaanUserManagement/Groups/add.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($group);
                $em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('kuma_user.group.add.flash.success', [
                        '%groupname%' => $group->getName(),
                    ])
                );

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_groups'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit a group
     *
     * @param int $id
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_groups_edit", methods={"GET", "POST"})
     * @Template("@KunstmaanUserManagement/Groups/edit.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /* @var Group $group */
        $group = $em->getRepository(Group::class)->find($id);
        $form = $this->createForm(GroupType::class, $group);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($group);
                $em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('kuma_user.group.edit.flash.success', [
                        '%groupname%' => $group->getName(),
                    ])
                );

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_groups'));
            }
        }

        return [
            'form' => $form->createView(),
            'group' => $group,
        ];
    }

    /**
     * Delete a group
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_groups_delete", methods={"POST"})
     *
     * @throws AccessDeniedException
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $configurator = new GroupAdminListConfigurator($em);

        /** @var SlugifierInterface $slugifier */
        $slugifier = $this->container->get('kunstmaan_utilities.slugifier');

        if (!$this->isCsrfTokenValid('delete-' . $slugifier->slugify($configurator->getEntityName()), $request->request->get('token'))) {
            $indexUrl = $configurator->getIndexUrl();

            return new RedirectResponse($this->generateUrl($indexUrl['path'], $indexUrl['params'] ?? []));
        }

        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Group::class)->find($id);
        if (!\is_null($group)) {
            $em->remove($group);
            $em->flush();

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->translator->trans('kuma_user.group.delete.flash.success', [
                    '%groupname%' => $group->getName(),
                ])
            );
        }

        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_groups'));
    }
}
