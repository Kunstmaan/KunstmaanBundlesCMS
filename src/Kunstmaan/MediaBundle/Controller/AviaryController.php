<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * controllerclass which Aviary can use to upload the edited image and add it to the database
 */
class AviaryController extends Controller
{

    /**
     * @param int $galleryId The id of the Gallery
     * @param int $imageId   The id of the image
     *
     * @Route("/aviary/{galleryId}/{imageId}", requirements={"galleryId" = "\d+", "imageId" = "\d+"}, name="KunstmaanMediaBundle_aviary")
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function indexAction($galleryId, $imageId)
    {
        $em = $this->getDoctrine()->getManager();

        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($galleryId);

        $helper = new MediaHelper();
        $helper->getMediaFromUrl($this->getRequest()->get('url'));

        $hulp = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($imageId);
        $picture = new Image();
        $picture->setOriginal($hulp);
        $picture->setName($hulp->getName()."-edited");
        $picture->setContent($helper->getMedia());
        $picture->setGallery($gallery);

        $em->persist($picture);
        $em->flush();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    }
}
