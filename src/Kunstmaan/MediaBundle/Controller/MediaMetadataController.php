<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Kunstmaan\MediaBundle\Entity\File;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MediaBundle\Event\MediaEvent;
use Kunstmaan\MediaBundle\Event\Events;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata;

use Kunstmaan\MediaBundle\Entity\Media;

use Kunstmaan\MediaBundle\Helper\MediaManager;

/**
 * MediaMetadataController
 */
class MediaMetadataController extends Controller
{
    /**
     * @param int $mediaId
     *
     * @Route("/{mediaId}/edit", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_media_meta_data_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editAction($mediaId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Media $media */
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);

        $metadataClass = $this->getMetadataClass($media->getContext());
        if (isset($metadataClass)) {
            $classMetadata = $em->getClassMetadata($metadataClass);
            $repo = new EntityRepository($em, $classMetadata);

            $result = $repo->findByMedia($media->getId());

            /* @var AbstractMediaMetadata $metadata */
            $metadata = null;
            if (!empty($result)) {
                $metadata = $result[0];
            } else {
                $metadata = new $metadataClass();
            }

            $form = $this->createForm($metadata->getDefaultAdminType(), $metadata);
        } else {
            // return to show
            return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'mediaId' => $media->getId() )));
        }

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {

                if (isset($metadata)) {
                    $metadata->setMedia($media);
                    $em->persist($metadata);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::POST_EDIT)) {
                    $event = new MediaEvent($media, isset($metadata)? $metadata : null);
                    $dispatcher->dispatch(Events::POST_EDIT, $event);
                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'mediaId' => $media->getId() )));
            }
        }

        return array(
            'form' => $form->createView(),
            'media' => $media,
            'gallery' => $media->getGallery()
        );
    }

    /**
     * @param string $context
     *
     * @return AbstractMediaMetadata
     */
    private function getMetadataClass($context = File::CONTEXT)
    {
        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.manager');
        $imageContext = $mediaManager->getContext($context);

        return $imageContext->getMetadataClass();
    }

}
