<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Entity\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * chooser controller.
 *
 */
class ChooserController extends Controller
{

    /**
     * @Route("/ckeditor", name="KunstmaanMediaBundle_chooser_ckeditor")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function ckeditorAction()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var string $funcNum */
        $funcNum = $this->getRequest()->get("CKEditorFuncNum");
        /* @var Folder $firstGallery */
        $firstGallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_ckeditor_show", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug(), "CKEditorFuncNum" => $funcNum)));
    }

    /**
     * @param int $id
     *
     * @Route("/ckeditor/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_ckeditor_show")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function ckeditorShowFolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var string $funcNum */
        $funcNum = $this->getRequest()->get("CKEditorFuncNum");
        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        /* @var array $galleries */
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'         => $folder,
            'galleries'       => $galleries,
            "CKEditorFuncNum" => $funcNum
        );
    }

    /**
     * @Route("/imagechooser", name="KunstmaanMediaBundle_chooser_image_chooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function imageChooserAction()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $firstGallery */
        $firstGallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_image_chooser_show_folder", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
    }

    /**
     * @Route("/filechooser", name="KunstmaanMediaBundle_chooser_file_chooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function fileChooserAction()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $firstGallery */
        $firstGallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_file_chooser_show_folder", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
    }

    /**
     * @Route("/slidechooser", name="KunstmaanMediaBundle_chooser_slide_chooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function slideChooserAction()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $firstGallery */
        $firstGallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_slide_chooser_show_folder", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
    }

    /**
     * @Route("/videochooser", name="KunstmaanMediaBundle_chooser_video_chooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function videoChooserAction()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $firstGallery */
        $firstGallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_video_chooser_show_folder", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
    }

    /**
     * @param int $folderId
     *
     * @Route("/filechooser/{folderId}/{slug}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_file_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function fileChooserShowFolderAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        /* @var array $galleries */
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("/imagechooser/{folderId}/{slug}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_image_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function imageChooserShowFolderAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        /* @var array $galleries */
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("/slidechooser/{folderId}/{slug}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_slide_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function slideChooserShowFolderAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        /* @var array $galleries */
        $folders = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'folders'     => $folders
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("/videochooser/{folderId}/{slug}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_video_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function videoChooserShowFolderAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        /* @var array $folders */
        $folders = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'folders'     => $folders
        );
    }

}