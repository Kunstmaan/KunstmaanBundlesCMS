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

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_imagechooser_show", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
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

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_filechooser_show", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
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

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_slidechooser_show", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
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

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_videochooser_show", array("folderId"=>$firstGallery->getId(), "slug" => $firstGallery->getSlug())));
    }

    /**
     * @param int $id
     *
     * @Route("/filechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_file_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function fileChooserShowFolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        /* @var array $galleries */
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $id
     *
     * @Route("/imagechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_image_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function imageChooserShowFolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        /* @var array $galleries */
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $id
     *
     * @Route("/slidechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_slide_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function slideChooserShowFolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        /* @var array $galleries */
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $id
     *
     * @Route("/videochooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_video_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function videoChooserShowFolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        /* @var array $galleries */
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $folder,
            'galleries'     => $galleries
        );
    }

}