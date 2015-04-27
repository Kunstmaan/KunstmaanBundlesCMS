<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\AdminList\MediaAdminListConfigurator;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Form\FolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FolderController.
 */
class FolderController extends Controller
{
    /**
     * @param Request $request
     * @param int     $folderId The folder id
     *
     * @Route("/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_show")
     * @Template()
     *
     * @return array
     */
    public function showAction(Request $request, $folderId)
    {
        /** @var EntityManager $em */
        $em      = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        // Check when user switches between thumb -and list view
        $viewMode = $request->query->get('viewMode');
        if ($viewMode && $viewMode == 'list-view') {
            $session->set('media-list-view', true);
        } elseif ($viewMode && $viewMode == 'thumb-view') {
            $session->remove('media-list-view');
        }

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $adminListConfigurator = new MediaAdminListConfigurator($em, $mediaManager, $folder, $request);
        $adminList             = $this->get('kunstmaan_adminlist.factory')->createList($adminListConfigurator);
        $adminList->bindRequest($request);

        $sub = new Folder();
        $sub->setParent($folder);
        $subForm  = $this->createForm(new FolderType($sub), $sub);
        $editForm = $this->createForm(new FolderType($folder), $folder);

        if ($request->isMethod('POST')) {
            $editForm->handleRequest($request);
            if ($editForm->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($folder);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Folder \'' . $folder->getName() . '\' has been updated!'
                );

                return new RedirectResponse(
                    $this->generateUrl(
                        'KunstmaanMediaBundle_folder_show',
                        array('folderId' => $folderId)
                    )
                );
            }
        }

        return array(
            'foldermanager' => $this->get('kunstmaan_media.folder_manager'),
            'mediamanager'  => $this->get('kunstmaan_media.media_manager'),
            'subform'       => $subForm->createView(),
            'editform'      => $editForm->createView(),
            'folder'        => $folder,
            'adminlist'     => $adminList,
            'type'          => null,
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("/delete/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_delete")
     *
     * @return RedirectResponse
     */
    public function deleteAction($folderId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder       = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $folderName   = $folder->getName();
        $parentFolder = $folder->getParent();

        if (is_null($parentFolder)) {
            $this->get('session')->getFlashBag()->add(
                'failure',
                'You can\'t delete the \'' . $folderName . '\' folder!'
            );
        } else {
            $em->getRepository('KunstmaanMediaBundle:Folder')->delete($folder);
            $this->get('session')->getFlashBag()->add('success', 'Folder \'' . $folderName . '\' has been deleted!');
            $folderId = $parentFolder->getId();
        }
        if (strpos($_SERVER['HTTP_REFERER'],'chooser')) {
            $redirect = 'KunstmaanMediaBundle_chooser_show_folder';
        } else $redirect = 'KunstmaanMediaBundle_folder_show';

        $type = $this->get('request')->get('type');

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
     * @param Request $request
     * @param int     $folderId
     *
     * @Route("/subcreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_sub_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return Response
     */
    public function subCreateAction(Request $request, $folderId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $parent */
        $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $folder = new Folder();
        $folder->setParent($parent);
        $form = $this->createForm(new FolderType(), $folder);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($folder);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Folder \'' . $folder->getName() . '\' has been created!'
                );
                if (strpos($_SERVER['HTTP_REFERER'],'chooser') !== false) {
                    $redirect = 'KunstmaanMediaBundle_chooser_show_folder';
                } else $redirect = 'KunstmaanMediaBundle_folder_show';

                $type = $request->get('type');

                return new RedirectResponse(
                    $this->generateUrl( $redirect,
                        array(
                            'folderId' => $folder->getId(),
                            'type' => $type,
                        )
                    )
                );
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();

        return $this->render(
            'KunstmaanMediaBundle:Folder:addsub-modal.html.twig',
            array(
                'subform'   => $form->createView(),
                'galleries' => $galleries,
                'folder'    => $folder,
                'parent'    => $parent
            )
        );
    }
}
