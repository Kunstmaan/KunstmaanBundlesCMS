<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\Folder;
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
     * @param int $folderId The id of the Folder
     * @param int $imageId  The id of the image
     *
     * @Route("/aviary/{folderId}/{imageId}", requirements={"folderId" = "\d+", "imageId" = "\d+"}, name="KunstmaanMediaBundle_aviary")
     * @return RedirectResponse
     */
    public function indexAction($folderId, $imageId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $helper = new MediaHelper();
        $helper->getMediaFromUrl($this->getRequest()->get('url'));

        /* @var Image $media */
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($imageId);
        $picture = new Image();
        $picture->setOriginal($media);
        $picture->setName($media->getName()."-edited");
        $picture->setContent($helper->getMedia());
        $picture->setGallery($folder);

        $em->persist($picture);
        $em->flush();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId' => $folder->getId(), 'slug' => $folder->getSlug())));
    }
}
