<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Helper\File\FileHelper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
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
     * @param int $mediaId  The id of the image
     *
     * @Route("/aviary/{folderId}/{mediaId}", requirements={"folderId" = "\d+", "mediaId" = "\d+"}, name="KunstmaanMediaBundle_aviary")
     * @return RedirectResponse
     */
    public function indexAction($folderId, $mediaId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        /* @var Media $media */
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');

        $handler = $mediaManager->getHandler($media);
        $fileHelper = $handler->getFormTypeHelper($media);
        $fileHelper->getMediaFromUrl($this->getRequest()->get('url'));
        $media = $fileHelper->getMedia();

        $em->persist($media);
        $em->flush();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId' => $folder->getId())));
    }
}
