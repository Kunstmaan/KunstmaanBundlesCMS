<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\MediaBundle\AdminList\MediaAdminListConfigurator;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Helper\FolderManager;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FolderController extends AbstractController
{
    /** @var MediaManager */
    private $mediaManager;
    /** @var FolderManager */
    private $folderManager;
    /** @var AdminListFactory */
    private $adminListFactory;
    /** @var RequestStack */
    private $requestStack;
    /** @var TranslatorInterface */
    private $translator;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        MediaManager $mediaManager,
        FolderManager $folderManager,
        AdminListFactory $adminListFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        EntityManagerInterface $em
    ) {
        $this->mediaManager = $mediaManager;
        $this->folderManager = $folderManager;
        $this->adminListFactory = $adminListFactory;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * @param int $folderId The folder id
     *
     * @Route("/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_show")
     */
    public function showAction(Request $request, $folderId): Response
    {
        $session = $request->getSession();

        // Check when user switches between thumb -and list view
        $viewMode = $request->query->get('viewMode');
        if ($viewMode && $viewMode == 'list-view') {
            $session->set('media-list-view', true);
        } elseif ($viewMode && $viewMode == 'thumb-view') {
            $session->remove('media-list-view');
        }

        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);

        $adminListConfigurator = new MediaAdminListConfigurator($this->em, $this->mediaManager, $folder, $request);
        $adminList = $this->adminListFactory->createList($adminListConfigurator);
        $adminList->bindRequest($request);

        $sub = new Folder();
        $sub->setParent($folder);
        $subForm = $this->createForm(FolderType::class, $sub, ['folder' => $sub]);

        $emptyForm = $this->createEmptyForm();

        $editForm = $this->createForm(FolderType::class, $folder, ['folder' => $folder]);

        if ($request->isMethod('POST')) {
            $editForm->handleRequest($request);
            if ($editForm->isValid()) {
                $this->em->getRepository(Folder::class)->save($folder);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('media.folder.show.success.text', [
                        '%folder%' => $folder->getName(),
                    ])
                );

                return $this->redirectToRoute('KunstmaanMediaBundle_folder_show', ['folderId' => $folderId]);
            }
        }

        return $this->render('@KunstmaanMedia/Folder/show.html.twig', [
            'foldermanager' => $this->folderManager,
            'mediamanager' => $this->mediaManager,
            'subform' => $subForm->createView(),
            'emptyform' => $emptyForm->createView(),
            'editform' => $editForm->createView(),
            'folder' => $folder,
            'adminlist' => $adminList,
            'type' => null,
        ]);
    }

    /**
     * @param int $folderId
     *
     * @Route("/delete/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_delete", methods={"POST"})
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $folderId)
    {
        if (!$this->isCsrfTokenValid('media-folder-delete', $request->request->get('token'))) {
            return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', ['folderId' => $folderId]));
        }

        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);
        $folderName = $folder->getName();
        $parentFolder = $folder->getParent();

        if (\is_null($parentFolder)) {
            $this->addFlash(
                FlashTypes::DANGER,
                $this->translator->trans('media.folder.delete.failure.text', [
                    '%folder%' => $folderName,
                ])
            );
        } else {
            $this->em->getRepository(Folder::class)->delete($folder);
            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->translator->trans('media.folder.delete.success.text', [
                    '%folder%' => $folderName,
                ])
            );
            $folderId = $parentFolder->getId();
        }
        if (strpos($request->server->get('HTTP_REFERER', ''), 'chooser')) {
            $redirect = 'KunstmaanMediaBundle_chooser_show_folder';
        } else {
            $redirect = 'KunstmaanMediaBundle_folder_show';
        }

        $type = $this->requestStack->getCurrentRequest()->get('type');

        return $this->redirectToRoute($redirect, [
            'folderId' => $folderId,
            'type' => $type,
        ]);
    }

    /**
     * @param int $folderId
     *
     * @Route("/subcreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_sub_create", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function subCreateAction(Request $request, $folderId)
    {
        /* @var Folder $parent */
        $parent = $this->em->getRepository(Folder::class)->getFolder($folderId);
        $folder = new Folder();
        $folder->setParent($parent);
        $form = $this->createForm(FolderType::class, $folder);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->getRepository(Folder::class)->save($folder);
                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('media.folder.addsub.success.text', [
                        '%folder%' => $folder->getName(),
                    ])
                );
                if (strpos($request->server->get('HTTP_REFERER', ''), 'chooser') !== false) {
                    $redirect = 'KunstmaanMediaBundle_chooser_show_folder';
                } else {
                    $redirect = 'KunstmaanMediaBundle_folder_show';
                }

                $type = $request->query->get('type');

                return $this->redirectToRoute($redirect, [
                    'folderId' => $folder->getId(),
                    'type' => $type,
                ]);
            }
        }

        $galleries = $this->em->getRepository(Folder::class)->getAllFolders();

        return $this->render('@KunstmaanMedia/Folder/addsub-modal.html.twig', [
            'subform' => $form->createView(),
            'galleries' => $galleries,
            'folder' => $folder,
            'parent' => $parent,
        ]);
    }

    /**
     * @param int $folderId
     *
     * @Route("/empty/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_empty", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function emptyAction(Request $request, $folderId)
    {
        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);

        $form = $this->createEmptyForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $alsoDeleteFolders = $data['checked'];

                $this->em->getRepository(Folder::class)->emptyFolder($folder, $alsoDeleteFolders);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans('media.folder.empty.success.text', [
                        '%folder%' => $folder->getName(),
                    ])
                );
                if (strpos($request->server->get('HTTP_REFERER', ''), 'chooser') !== false) {
                    $redirect = 'KunstmaanMediaBundle_chooser_show_folder';
                } else {
                    $redirect = 'KunstmaanMediaBundle_folder_show';
                }

                return $this->redirectToRoute($redirect, [
                    'folderId' => $folder->getId(),
                    'folder' => $folder,
                ]);
            }
        }

        return $this->render('@KunstmaanMedia/Folder/empty-modal.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reorder", name="KunstmaanMediaBundle_folder_reorder")
     *
     * @return JsonResponse
     */
    public function reorderAction(Request $request)
    {
        $folders = [];
        $nodeIds = $request->request->get('nodes');

        $repository = $this->em->getRepository(Folder::class);

        foreach ($nodeIds as $id) {
            /* @var Folder $folder */
            $folder = $repository->find($id);
            $folders[] = $folder;
        }

        foreach ($folders as $id => $folder) {
            $repository->moveDown($folder, true);
        }

        $this->em->flush();

        return new JsonResponse([
            'Success' => 'The node-translations for have got new weight values',
        ]);
    }

    private function createEmptyForm()
    {
        $defaultData = ['checked' => false];
        $form = $this->createFormBuilder($defaultData)
            ->add('checked', CheckboxType::class, ['required' => false, 'label' => 'media.folder.empty.modal.checkbox'])
            ->getForm();

        return $form;
    }
}
