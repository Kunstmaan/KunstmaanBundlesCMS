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
 * @author Kristof Van Cauwenbergh
 */
class ChooserController extends Controller
{
    /**
     * @Route("/ckeditor", name="KunstmaanMediaBundle_chooser_ckeditor")
     * @Template()
     */
    public function ckeditorAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1, $em);
        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_ckeditor_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/imagechooser", name="KunstmaanMediaBundle_chooser_imagechooser")
     * @Template()
     */
    public function imagechooserAction() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1, $em);
    	return $this->redirect($this->generateUrl("KunstmaanMediaBundle_imagechooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/filechooser", name="KunstmaanMediaBundle_chooser_filechooser")
     * @Template()
     */
    public function filechooserAction() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1, $em);
    	return $this->redirect($this->generateUrl("KunstmaanMediaBundle_filechooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/slidechooser", name="KunstmaanMediaBundle_chooser_slidechooser")
     * @Template()
     */
    public function slidechooserAction() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1, $em);
    	return $this->redirect($this->generateUrl("KunstmaanMediaBundle_slidechooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/videochooser", name="KunstmaanMediaBundle_chooser_videochooser")
     * @Template()
     */
    public function videochooserAction() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$firstgallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1, $em);
    	return $this->redirect($this->generateUrl("KunstmaanMediaBundle_videochooser_show", array("id"=>$firstgallery->getId(), "slug" => $firstgallery->getSlug())));
    }

    /**
     * @Route("/filechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_filechooser_show")
     * @Template()
     */
    function filechoosershowfolderAction($id){
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id, $em);
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

    	return array(
    			'gallery'       => $gallery,
    			'galleries'     => $galleries
    	);
    }

    /**
     * @Route("/imagechooser/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_imagechooser_show")
     * @Template()
     */
    function imagechoosershowfolderAction($id){
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id, $em);
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

    	return array(
    			'gallery'       => $gallery,
    			'galleries'     => $galleries
    	);
    }

    /**
     * @Route("/slide/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_slidechooser_show")
     * @Template()
     */
    function slidechoosershowfolderAction($id){
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id, $em);
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

    	return array(
    			'gallery'       => $gallery,
    			'galleries'     => $galleries
    	);
    }

    /**
     * @Route("/video/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_videochooser_show")
     * @Template()
     */
    function videochoosershowfolderAction($id){
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id, $em);
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

    	return array(
    			'gallery'       => $gallery,
    			'galleries'     => $galleries
    	);
    }

    /**
     * @Route("/ckeditor/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_ckeditor_show")
     * @Template()
     */
    function ckeditorshowfolderAction($id){
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id, $em);
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

    	return array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
    	);
    }

}