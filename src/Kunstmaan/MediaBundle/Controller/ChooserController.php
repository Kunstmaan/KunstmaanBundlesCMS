<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Form\SubFolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        $funcnum = $this->getRequest()->get("CKEditorFuncNum");
        $firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_ckeditor_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug(), "CKEditorFuncNum" => $funcnum)));
    }

    /**
     * @param int $id
     *
     * @Route("/ckeditor/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_ckeditor_show")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function ckeditorshowfolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();
        $funcnum = $this->getRequest()->get("CKEditorFuncNum");

        return array(
            'gallery'         => $gallery,
            'galleries'       => $galleries,
            "CKEditorFuncNum" => $funcnum
        );
    }

    /**
     * @Route("/imagechooser", name="KunstmaanMediaBundle_chooser_imagechooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function imagechooserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_imagechooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/filechooser", name="KunstmaanMediaBundle_chooser_filechooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function filechooserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_filechooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/slidechooser", name="KunstmaanMediaBundle_chooser_slidechooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function slidechooserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_slidechooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/videochooser", name="KunstmaanMediaBundle_chooser_videochooser")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function videochooserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_videochooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @param int $id
     *
     * @Route("/filechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_filechooser_show")
     * @Template()
     *
     * @return array
     */
    public function filechoosershowfolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $id
     *
     * @Route("/imagechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_imagechooser_show")
     * @Template()
     *
     * @return array
     */
    public function imagechoosershowfolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $id
     *
     * @Route("/slidechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_slidechooser_show")
     * @Template()
     *
     * @return array
     */
    public function slidechoosershowfolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
        );
    }

    /**
     * @param int $id
     *
     * @Route("/videochooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_videochooser_show")
     * @Template()
     *
     * @return array
     */
    public function videochoosershowfolderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
        );
    }

}