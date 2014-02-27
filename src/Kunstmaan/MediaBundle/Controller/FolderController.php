<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\AdminList\MediaAdminListConfigurator;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Form\FolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * FolderController.
 */
class FolderController extends Controller
{
    /**
     * @param int $folderId The folder id
     *
     * @Route("/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_show")
     * @Template()
     *
     * @return array
     */
    public function showAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
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
        $folders = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();

        $adminListConfigurator = new MediaAdminListConfigurator($em, null, $mediaManager, $folder, $request);
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList($adminListConfigurator);
        $adminList->bindRequest($request);

        $sub = new Folder();
        $sub->setParent($folder);
        $subForm = $this->createForm(new FolderType($sub), $sub);
        $editForm = $this->createForm(new FolderType($folder), $folder);

        if ('POST' == $request->getMethod()) {
            $editForm->submit($request);
            if ($editForm->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($folder);

                $this->get('session')->getFlashBag()->add('success', 'Folder \''.$folder->getName().'\' has been updated!');
            }
        }

        return array(
            'mediamanager'  => $this->get('kunstmaan_media.media_manager'),
            'subform'       => $subForm->createView(),
            'editform'      => $editForm->createView(),
            'folder'        => $folder,
            'folders'       => $folders,
            'adminlist'     => $adminList
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
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $folderName = $folder->getName();
        $parentFolder = $folder->getParent();

        if (empty($parentFolder)) {
            $this->get('session')->getFlashBag()->add('failure', 'You can\'t delete the \''.$folderName.'\' Folder!');
        } else {
            $folder->setDeleted(true);
            $em->persist($folder);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Folder \''.$folderName.'\' has been deleted!');
            $folderId = $parentFolder->getId();
        }

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folderId)));
    }

    /**
     * @param int $folderId
     *
     * @Route("/subcreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_sub_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return Response
     */
    public function subCreateAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $parent */
        $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $folder = new Folder();
        $folder->setParent($parent);
        $form = $this->createForm(new FolderType(), $folder);
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($folder);

                $this->get('session')->getFlashBag()->add('success', 'Folder \''.$folder->getName().'\' has been created!');

                return new Response('<script>window.location="'.$this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId' => $folder->getId())).'"</script>');
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();

        return $this->render('KunstmaanMediaBundle:Folder:addsub-modal.html.twig', array(
            'subform' => $form->createView(),
            'galleries' => $galleries,
            'folder' => $folder,
            'parent' => $parent
        ));
    }

}