<?php

namespace Kunstmaan\UserManagementBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Role;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Form\RoleType;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\UserManagementBundle\AdminList\RoleAdminListConfigurator;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Settings controller handling everything related to creating, editing, deleting and listing roles in an admin list
 */
final class RolesController extends AbstractController
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var AdminListFactory */
    private $adminListFactory;
    /** @var EntityManagerInterface */
    private $em;
    /** @var SlugifierInterface */
    private $slugifier;

    public function __construct(TranslatorInterface $translator, AdminListFactory $adminListFactory, EntityManagerInterface $em, SlugifierInterface $slugifier)
    {
        $this->translator = $translator;
        $this->adminListFactory = $adminListFactory;
        $this->em = $em;
        $this->slugifier = $slugifier;
    }

    /**
     * List roles
     *
     * @Route("/", name="KunstmaanUserManagementBundle_settings_roles")
     * @Template("@KunstmaanAdminList/Default/list.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $adminlist = $this->adminListFactory->createList(new RoleAdminListConfigurator($this->em));
        $adminlist->bindRequest($request);

        return [
            'adminlist' => $adminlist,
        ];
    }

    /**
     * Add a role
     *
     * @Route("/add", name="KunstmaanUserManagementBundle_settings_roles_add", methods={"GET", "POST"})
     * @Template("@KunstmaanUserManagement/Roles/add.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array|RedirectResponse
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $role = new Role('');
        $form = $this->createForm(RoleType::class, $role);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($role);
                $this->em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('kuma_user.roles.add.flash.success.%role%', [
                        '%role%' => $role->getRole(),
                    ])
                );

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_roles'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit a role
     *
     * @param int $id
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_roles_edit", methods={"GET", "POST"})
     * @Template("@KunstmaanUserManagement/Roles/edit.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var Role $role */
        $role = $this->em->getRepository(Role::class)->find($id);
        $form = $this->createForm(RoleType::class, $role);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($role);
                $this->em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('kuma_user.roles.edit.flash.success.%role%', [
                        '%role%' => $role->getRole(),
                    ])
                );

                return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_roles'));
            }
        }

        return [
            'form' => $form->createView(),
            'role' => $role,
        ];
    }

    /**
     * Delete a role
     *
     * @param int $id
     *
     * @Route ("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanUserManagementBundle_settings_roles_delete", methods={"POST"})
     *
     * @throws AccessDeniedException
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $configurator = new RoleAdminListConfigurator($this->em);

        if (!$this->isCsrfTokenValid('delete-' . $this->slugifier->slugify($configurator->getEntityName()), $request->request->get('token'))) {
            $indexUrl = $configurator->getIndexUrl();

            return new RedirectResponse($this->generateUrl($indexUrl['path'], $indexUrl['params'] ?? []));
        }

        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var Role $role */
        $role = $this->em->getRepository(Role::class)->find($id);
        if (!\is_null($role)) {
            $this->em->remove($role);
            $this->em->flush();

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->translator->trans('kuma_user.roles.delete.flash.success.%role%', [
                    '%role%' => $role->getRole(),
                ])
            );
        }

        return new RedirectResponse($this->generateUrl('KunstmaanUserManagementBundle_settings_roles'));
    }
}
