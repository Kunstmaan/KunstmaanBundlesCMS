<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\MediaBundle\AdminList\MediaAdminListConfigurator;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Form\EmptyType;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class FolderController
 * @package Kunstmaan\MediaBundle\Controller
 */
class FolderController extends Controller
{
    /** @var EntityManager $em */
    private $em;

    /** @var AdminList $adminList */
    private $adminList;

    /** @var MediaManager $mediaManager */
    private $mediaManager;

    /** @var Form $editForm */
    private $editForm;

    /** @var Form $subForm */
    private $subForm;

    /** @var Form $emptyForm */
    private $emptyForm;

    /** @var Folder $folder */
    private $folder;

    /**
     * @Route("/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_show")
     * @Template()
     * @param Request $request
     * @param $folderId
     * @throws \Exception
     *
     * @return array|RedirectResponse
     */
    public function showAction(Request $request, $folderId)
    {
        $this->em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $this->setViewMode($request, $session);
        $this->prepareForms($request, $folderId);

        if ($request->isMethod('POST')) {
            $this->editForm->handleRequest($request);
            if ($this->editForm->isValid()) {
                $this->em->getRepository('KunstmaanMediaBundle:Folder')->save($this->folder);
                $translation = $this->get('translator')->trans('media.folder.show.success.text', ['%folder%' => $this->folder->getName()]);
                $this->addFlash(FlashTypes::SUCCESS, $translation);
                $url = $this->generateUrl('KunstmaanMediaBundle_folder_show',['folderId' => $folderId]);
                return new RedirectResponse($url);
            }
        }

        return $this->getShowActionViewVars();
    }

    /**
     * @param Request $request
     * @param $folderId
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    private function prepareForms(Request $request, $folderId)
    {
        $this->mediaManager = $this->get('kunstmaan_media.media_manager');
        $this->folder = $this->em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $adminListConfigurator = new MediaAdminListConfigurator($this->em, $this->mediaManager, $this->folder, $request);
        $this->adminList  = $this->get('kunstmaan_adminlist.factory')->createList($adminListConfigurator);
        $this->adminList->bindRequest($request);
        $sub = new Folder();
        $sub->setParent($this->folder);
        $this->subForm  = $this->createForm(FolderType::class, $sub, ['folder' => $sub]);
        $this->emptyForm = $this->createEmptyForm();
        $this->editForm = $this->createForm(FolderType::class, $this->folder, ['folder' => $this->folder]);
    }

    /**
     * @return array
     */
    private function getShowActionViewVars()
    {
        return [
            'foldermanager' => $this->get('kunstmaan_media.folder_manager'),
            'mediamanager'  => $this->get('kunstmaan_media.media_manager'),
            'subform'       => $this->subForm->createView(),
            'emptyform'     => $this->emptyForm->createView(),
            'editform'      => $this->editForm->createView(),
            'folder'        => $this->folder,
            'adminlist'     => $this->adminList,
            'type'          => null,
        ];
    }


    /**
     * @param Request $request
     * @param SessionInterface $session
     */
    private function setViewMode(Request $request, SessionInterface $session)
    {
        // Check when user switches between thumb -and list view
        $viewMode = $request->query->get('viewMode');
        if ($viewMode && $viewMode == 'list-view') {
            $session->set('media-list-view', true);
        } elseif ($viewMode && $viewMode == 'thumb-view') {
            $session->remove('media-list-view');
        }
    }


    /**
     * @Route("/delete/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_delete")
     * @param Request $request
     * @param $folderId
     * @throws \Doctrine\ORM\EntityNotFoundException
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $folderId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder       = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $folderName   = $folder->getName();
        $parentFolder = $folder->getParent();

        if (is_null($parentFolder)) {
            $this->addFlash(
                FlashTypes::ERROR,
                $this->get('translator')->trans('media.folder.delete.failure.text', array(
                    '%folder%' => $folderName
                ))
            );
        } else {
            $em->getRepository('KunstmaanMediaBundle:Folder')->delete($folder);
            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->get('translator')->trans('media.folder.delete.success.text', array(
                    '%folder%' => $folderName
                ))
            );
            $folderId = $parentFolder->getId();
        }
        if (strpos($request->server->get('HTTP_REFERER', ''),'chooser')) {
            $redirect = 'KunstmaanMediaBundle_chooser_show_folder';
        } else $redirect = 'KunstmaanMediaBundle_folder_show';

        $type = $this->get('request_stack')->getCurrentRequest()->get('type');

        return new RedirectResponse(
            $this->generateUrl($redirect,
                array(
                    'folderId' => $folderId,
                    'type' => $type,
                )
            )
        );
    }

    /**
     * @Route("/subcreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_sub_create")
     * @param Request $request
     * @param $folderId
     * @throws \Exception
     *
     * @return RedirectResponse|Response
     */
    public function subCreateAction(Request $request, $folderId)
    {
        /** @var EntityManager $em */
        $this->em = $this->getDoctrine()->getManager();
        /* @var Folder $parent */
        $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $this->folder = new Folder();
        $this->folder->setParent($parent);
        $form = $this->createForm(FolderType::class, $this->folder);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                return $this->subCreateSaveAndRedirect($request);
            }
        }
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();

        return $this->render('KunstmaanMediaBundle:Folder:addsub-modal.html.twig', [
            'subform' => $form->createView(), 'galleries' => $galleries, 'folder' => $this->folder, 'parent' => $parent
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    private function subCreateSaveAndRedirect(Request $request)
    {
        $this->em->getRepository('KunstmaanMediaBundle:Folder')->save($this->folder);
        $translation = $this->get('translator')->trans('media.folder.addsub.success.text', ['%folder%' => $this->folder->getName()]);
        $this->addFlash(FlashTypes::SUCCESS, $translation);
        $redirect = (strpos($request->server->get('HTTP_REFERER', ''),'chooser') !== false)
            ? 'KunstmaanMediaBundle_chooser_show_folder'
            : 'KunstmaanMediaBundle_folder_show';
        $type = $request->get('type');
        $url = $this->generateUrl( $redirect, ['folderId' => $this->folder->getId(), 'type' => $type]);
        return new RedirectResponse($url);
    }

    /**
     * @Route("/empty/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_empty")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param $folderId
     * @throws \Doctrine\ORM\EntityNotFoundException
     *
     * @return RedirectResponse|Response
     */
    public function emptyAction(Request $request, $folderId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $form = $this->createEmptyForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();
                $alsoDeleteFolders = $data['checked'];

                $em->getRepository('KunstmaanMediaBundle:Folder')->emptyFolder($folder, $alsoDeleteFolders);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->get('translator')->trans('media.folder.empty.success.text', array(
                        '%folder%' => $folder->getName()
                    ))
                );
                if (strpos($request->server->get('HTTP_REFERER', ''),'chooser') !== false) {
                    $redirect = 'KunstmaanMediaBundle_chooser_show_folder';
                } else $redirect = 'KunstmaanMediaBundle_folder_show';

                return new RedirectResponse(
                    $this->generateUrl( $redirect,
                        array(
                            'folderId' => $folder->getId(),
                            'folder' => $folder
                        )
                    )
                );

            }
        }

        return $this->render(
            'KunstmaanMediaBundle:Folder:empty-modal.html.twig',
            array(
                'form'   => $form->createView(),
            )
        );
    }

    /**
     * @Route("/reorder", name="KunstmaanMediaBundle_folder_reorder")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reorderAction(Request $request)
    {
        $folders         = array();
        $nodeIds       = $request->get('nodes');

        $em              = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('KunstmaanMediaBundle:Folder');

        foreach ($nodeIds as $id) {
            /* @var Folder $folder */
            $folder = $repository->find($id);
            $folders[] = $folder;
        }

        foreach ($folders as $id => $folder) {
            $repository->moveDown($folder, true);
        }

        $em->flush();

        return new JsonResponse(
            array(
                'Success' => 'The node-translations for have got new weight values'
            )
        );
    }

    /**
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    private function createEmptyForm()
    {
        $defaultData = ['checked' => false];
        $form = $this->createFormBuilder($defaultData)
            ->add('checked', CheckboxType::class, ['required' => false, 'label' => 'media.folder.empty.modal.checkbox'])
            ->getForm();
        return $form;
    }
}
