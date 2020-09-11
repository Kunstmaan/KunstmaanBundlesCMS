<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller class which Aviary can use to upload the edited image and add it to the database
 *
 * @deprecated The AviaryController is deprecated in KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. The aviary service is discontinued.
 */
class AviaryController extends Controller
{
    /**
     * @param int $folderId The id of the Folder
     * @param int $mediaId  The id of the image
     *
     * @Route("/aviary/{folderId}/{mediaId}", requirements={"folderId" = "\d+", "mediaId" = "\d+"}, name="KunstmaanMediaBundle_aviary")
     *
     * @return RedirectResponse
     */
    public function indexAction(Request $request, $folderId, $mediaId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository(Folder::class)->getFolder($folderId);
        /* @var Media $media */
        $media = $em->getRepository(Media::class)->getMedia($mediaId);
        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');

        $media = clone $media;
        $handler = $mediaManager->getHandler($media);
        $fileHelper = $handler->getFormHelper($media);
        $fileHelper->getMediaFromUrl($request->get('url'));
        $media = $fileHelper->getMedia();

        $media->setUuid(null);
        $handler->prepareMedia($media);

        $em->persist($media);
        $em->flush();

        $media->setCreatedAt($media->getUpdatedAt());
        $em->persist($media);
        $em->flush();

        return new RedirectResponse(
            $this->generateUrl(
                'KunstmaanMediaBundle_folder_show',
                ['folderId' => $folder->getId()]
            )
        );
    }
}
