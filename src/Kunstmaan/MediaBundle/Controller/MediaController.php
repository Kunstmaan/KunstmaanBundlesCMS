<?php

namespace Kunstmaan\MediaBundle\Controller;

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
        $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                        ->getAllGalleries();
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
        $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                        ->getAllGalleries();
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
        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();
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
        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                                ->getAllGalleries();
        $gallery = new FileGallery();

        return array(
            'gallery' => $gallery,
            'galleries' => $galleries
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
    	$galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
    	->getAllGalleries();
    
    	return $this->render('KunstmaanMediaBundle:'.ucfirst($gallery->getStrategy()->getType()).':show.html.twig', array(
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
    
    	return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    }    

}
