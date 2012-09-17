<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MediaBundle\Event\MediaEvent;
use Kunstmaan\MediaBundle\Event\Events;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Entity\Video;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Video controller.
 */
class VideoController extends Controller
{
    /**
     * @param int $mediaId
     *
     * @Route("/{mediaId}/edit", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_video_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editAction($mediaId)
    {
        $em = $this->getDoctrine()->getManager();

        $video = $em->getRepository('KunstmaanMediaBundle:Media')->find($mediaId);
        $video->setContent($video->getUuid());
        $request = $this->getRequest();

        $formbuilder = $this->createFormBuilder();
        $formbuilder->add('media', new VideoType());
        $bindingarray = array('media' => $video);

        $metadataClass = $this->getMetadataClass(Video::CONTEXT);
        if (isset($metadataClass)) {
            $classMetadata = $em->getClassMetadata($metadataClass);
            $repo = new EntityRepository($em, $classMetadata);

            $result = $repo->findByMedia($video->getId());

            if (!empty($result)) {
                $metadata = $result[0];
            } else {
                $metadata = new $metadataClass();
            }

            $formbuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingarray['metadata'] = $metadata;
        }

        $formbuilder->setData($bindingarray);
        $form = $formbuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $video->setUuid($video->getContent());

                $em->getRepository('KunstmaanMediaBundle:Media')->save($video);

                if (isset($metadata)) {
                    $metadata->setMedia($video);
                    $em->persist($metadata);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::POSTEDIT)) {
                    $event = new MediaEvent($video, $metadata);
                    $dispatcher->dispatch(Events::POSTEDIT, $event);
                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'media_id' => $video->getId() )));
            }
        }

        return array(
            'form' => $form->createView(),
            'media' => $video,
            'gallery' => $video->getGallery()
        );
    }

    /**
     * @param string $context
     *
     * @return AbstractMediaMetadata
     */
    private function getMetadataClass($context = File::CONTEXT)
    {
        $mediaManager = $this->get('kunstmaan_media.manager');
        $imageContext = $mediaManager->getContext($context);

        return $imageContext->getMetadataClass();
    }
}