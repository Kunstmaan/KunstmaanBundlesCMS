<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\AdminBundle\Modules\ClassLookup;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MediaController extends Controller
{
    /**
     * @Route("/", name="KunstmaanMediaBundle_media")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/images", name="KunstmaanMediaBundle_media_images")
     * @Template()
     */
    public function imagesAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                        ->getAllFoldersByType();
        $gallery = new ImageGallery();

        return array(
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    /**
     * @Route("/videos", name="KunstmaanMediaBundle_media_videos")
     * @Template()
     */
    public function videosAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                        ->getAllFoldersByType();
        $gallery = new VideoGallery();

        return array(
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    /**
     * @Route("/slides", name="KunstmaanMediaBundle_media_slides")
     * @Template()
     */
    public function slidesAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                        ->getAllFoldersByType();
        $gallery = new SlideGallery();

        return array(
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    /**
     * @Route("/files", name="KunstmaanMediaBundle_media_files")
     * @Template()
     */
    public function filesAction()
    {
        $em = $this->getDoctrine()
                           ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                ->getAllFoldersByType();
        $gallery = new FileGallery();

        return array(
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }
    /**
     * @Route("/folders", name="KunstmaanMediaBundle_media_folders")
     * @Template()
     */    
    public function foldersAction()
    {
    	$em = $this->getDoctrine()
    	->getEntityManager();
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
    					->getAllFoldersByType();
    	$folders = $em->getRepository('KunstmaanMediaBundle:Folder')
    					->getAllFolders();
    
    	return array(
    			'galleries' => $galleries,
    			'folders' => $folders
    	);
    }
    
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

}
