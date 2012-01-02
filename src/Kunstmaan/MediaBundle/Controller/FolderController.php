<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Form\VideoType;

use Kunstmaan\MediaBundle\Entity\Video;

use Kunstmaan\MediaBundle\Form\SlideType;

use Kunstmaan\MediaBundle\Entity\Slide;

use Kunstmaan\MediaBundle\Entity\Image;

use Kunstmaan\MediaBundle\Entity\File;

use Kunstmaan\MediaBundle\Form\MediaType;

use Kunstmaan\MediaBundle\Helper\MediaHelper;

use Kunstmaan\MediaBundle\Entity\Folder;

use Kunstmaan\MediaBundle\Helper\MediaList\MediaListConfigurator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class FolderController extends Controller
{
    /**
     * @Route("/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_folder_show")
     * @Template()
     */
    function showAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id, $em);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                ->getAllFoldersByType();

        $itemlist = "";
        $listconfigurator = $gallery->getStrategy()->getListConfigurator();
        if(isset($listconfigurator) && $listconfigurator != null){
            $itemlist = $this->get("adminlist.factory")->createList($listconfigurator, $em, array("gallery" => $gallery->getId()));
            $itemlist->bindRequest($this->getRequest());
        }

        //$form = $this->createForm($gallery->getStrategy()->getFormType(), $gallery->getStrategy()->getFormHelper());
        $sub = $gallery->getStrategy()->getNewGallery();
        $sub->setParent($gallery);
        $subform = $this->createForm(new SubGalleryType(), $sub);
        $editform = $this->createForm($gallery->getFormType($gallery), $gallery);

        return array(
            //'form'          => $form->createView(),
            'subform'       => $subform->createView(),
            'editform'      => $editform->createView(),
            'gallery'       => $gallery,
            'galleries'     => $galleries,
            'itemlist'      => $itemlist
        );
    }

    /**
     * @Route("/delete/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_delete")
     */
    public function deleteAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);
        
        $em->getRepository('KunstmaanMediaBundle:Folder')->delete($gallery, $em);

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                        ->getAllFoldersByType();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_'.$gallery->getStrategy()->getType().'s'));
    }

    /**
     * @Route("/update/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);
        
        $request = $this->getRequest();
        $form = $this->createForm($gallery->getFormType($gallery), $gallery);

            if ('POST' == $request->getMethod()) {
                $form->bindRequest($request);
                if ($form->isValid()){
                    $em->getRepository('KunstmaanMediaBundle:Folder')->save($gallery, $em);

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }

            $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                           ->getAllFoldersByType();

            return array(
                'gallery' => $gallery,
                'form' => $form->createView(),
                'galleries'     => $galleries
            );
     }

    /**
     * @Route("{type}/create", name="KunstmaanMediaBundle_folder_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($type)
    {
        $request = $this->getRequest();
        $form = $this->createForm(new GalleryType($type), new Folder());

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em = $this->getDoctrine()->getEntityManager();
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($gallery, $em);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
            }
        }

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                       ->getAllFoldersByType();

        return $this->render('KunstmaanMediaBundle:Folder:create.html.twig', array(
            'gallery' => $gallery,
            'form' => $form->createView(),
            'galleries'     => $galleries
        ));
    }

    /**
     * @Route("/subcreate/{id}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_folder_subcreate")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function subcreateAction($id)
    {
            $request = $this->getRequest();

            $em = $this->getDoctrine()->getEntityManager();
            $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id, $em);

            $gallery = $parent->getStrategy()->getNewGallery();
            $gallery->setParent($parent);
            $form = $this->createForm(new SubGalleryType(), $gallery);

            if ('POST' == $request->getMethod()) {
                $form->bindRequest($request);
                if ($form->isValid()){
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->getRepository('KunstmaanMediaBundle:Folder')->save($gallery, $em);

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }

            $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                           ->getAllFoldersByType();

            return $this->render('KunstmaanMediaBundle:Folder:subcreate.html.twig', array(
                'subform' => $form->createView(),
                'galleries' => $galleries,
                'gallery' => $gallery,
                'parent' => $parent
            ));
    }
    
    /**
     * @Route("filecreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_filecreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:File:create.html.twig")
     */
    public function filecreateAction($gallery_id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);
    
    	$request = $this->getRequest();
    	$helper = new MediaHelper();
    	$form = $this->createForm(new MediaType(), $helper);
    
    	if ('POST' == $request->getMethod()) {
    		$form->bindRequest($request);
    		if ($form->isValid()){
    			if ($helper->getMedia()!=null) {
    				$file = new File();
    				$file->setName($helper->getMedia()->getClientOriginalName());
    				$file->setContent($helper->getMedia());
    				$file->setGallery($gallery);
    
    				$em->getRepository('KunstmaanMediaBundle:Media')->save($file, $em);
    
    				return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    			}
    		}
    	}
    
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
    	->getAllFoldersByType();
    
    	return array(
    			'form' => $form->createView(),
    			'gallery' => $gallery,
    			'galleries' => $galleries
    	);
    }
    
    /**
     * @Route("imagecreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_imagecreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Image:create.html.twig")
     */
    public function imagecreateAction($gallery_id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);
    
    	$request = $this->getRequest();
    	$picturehelper = new MediaHelper();
    	$form = $this->createForm(new MediaType(), $picturehelper);
    
    	if ('POST' == $request->getMethod()) {
    		$form->bindRequest($request);
    		if ($form->isValid()){
    			if ($picturehelper->getMedia()!=null) {
    				$picture = new Image();
    				$picture->setName($picturehelper->getMedia()->getClientOriginalName());
    				$picture->setContent($picturehelper->getMedia());
    				$picture->setGallery($gallery);
    
    				$em->getRepository('KunstmaanMediaBundle:Media')->save($picture, $em);
    
    				return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    			}
    		}
    	}
    
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
    	->getAllFoldersByType();
    
    	return array(
    			'form' => $form->createView(),
    			'gallery' => $gallery,
    			'galleries' => $galleries
    	);
    }
    
    /**
     * @Route("videocreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_videocreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Video:create.html.twig")
     */
    public function videocreateAction($gallery_id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);
    
    	$request = $this->getRequest();
    	$Video = new Video();
    	$Video->setGallery($gallery);
    	$form = $this->createForm(new VideoType(), $Video);
    
    	if ('POST' == $request->getMethod()) {
    		$form->bindRequest($request);
    		if ($form->isValid()){
    			$em->getRepository('KunstmaanMediaBundle:Media')->save($Video, $em);
    
    			return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    		}
    	}
    
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
    	->getAllFoldersByType();
    
    	return array(
    			'form' => $form->createView(),
    			'gallery' => $gallery,
    			'galleries' => $galleries
    	);
    }
    
    /**
     * @Route("slidecreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_slidecreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Slide:create.html.twig")
     */
    public function slidecreateAction($gallery_id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);
    
    	$request = $this->getRequest();
    	$slide = new Slide();
    	$slide->setGallery($gallery);
    	$form = $this->createForm(new SlideType(), $slide);
    
    	if ('POST' == $request->getMethod()) {
    		$form->bindRequest($request);
    		if ($form->isValid()){
    			$em->getRepository('KunstmaanMediaBundle:Media')->save($slide, $em);
    
    			return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    		}
    	}
    
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
    	->getAllFoldersByType();
    	return array(
    			'form' => $form->createView(),
    			'gallery' => $gallery,
    			'galleries' => $galleries
    	);
    }
}