<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Entity\Video;
use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\File;
use Kunstmaan\MediaBundle\Form\MediaType;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Entity\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MediaController extends Controller
{
   
    /**
     * @Route("/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_media_show")
     */
    public function showAction($media_id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($media_id, $em);
    	$gallery = $media->getGallery();
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
    	->getAllFoldersByType();
    
    	return $this->render('KunstmaanMediaBundle:'.$media->getClassType().':show.html.twig', array(
    			'media' => $media,
    			'gallery' => $gallery,
    			'galleries' => $galleries
    	));
    }
    
    /**
     * @Route("/delete/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_media_delete")
     */
    public function deleteAction($media_id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($media_id, $em);
    	$gallery = $media->getGallery();
    	$em->getRepository('KunstmaanMediaBundle:Media')->delete($media, $em);
    
    	return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
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
