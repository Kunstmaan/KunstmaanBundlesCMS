<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\FolderType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * folder controller.
 *
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

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $folders = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();

        $sub = new Folder();
        $sub->setParent($folder);
        $subForm = $this->createForm(new FolderType($sub), $sub);
        $editForm = $this->createForm(new FolderType($folder), $folder);

        return array(
            'mediamanager'  => $this->get('kunstmaan_media.media_manager'),
            'subform'       => $subForm->createView(),
            'editform'      => $editForm->createView(),
            'folder'        => $folder,
            'folders'       => $folders,
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
        $foldername = $folder->getName();
        $parentFolder = $folder->getParent();

        $em->getRepository('KunstmaanMediaBundle:Folder')->delete($folder);

        $this->get('session')->getFlashBag()->add('success', 'Folder \''.$foldername.'\' has been deleted!');

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $parentFolder->getId())));
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

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId' => $folder->getId())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return $this->render('KunstmaanMediaBundle:Folder:subcreate.html.twig', array(
            'subform' => $form->createView(),
            'galleries' => $galleries,
            'folder' => $folder,
            'parent' => $parent
        ));
    }

}