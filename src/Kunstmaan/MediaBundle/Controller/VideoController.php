<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MediaBundle\Helper\Event\MediaEvent;
use Kunstmaan\MediaBundle\Helper\Event\Events;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Entity\Video;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Video controller.
 *
 */
class VideoController extends Controller
{
    /**
     * @Route("/{media_id}/edit", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_video_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $video = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);
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

            if(!empty($result)) {
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
            $form->bindRequest($request);
            if ($form->isValid()){
                $video->setUuid($video->getContent());

                $em->getRepository('KunstmaanMediaBundle:Media')->save($video, $em);

                if (isset($metadata)) {
                    $metadata->setMedia($video);
                    $em->persist($metadata);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::postEdit)) {
                    $event = new MediaEvent($video, $metadata);
                    $dispatcher->dispatch(Events::postEdit, $event);
                }

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'media_id' => $video->getId() )));
            }
        }

        return array(
            'form' => $form->createView(),
            'media' => $video,
            'gallery' => $video->getGallery()
        );
    }

    private function getMetadataClass($context = File::CONTEXT)
    {
        $mediaManager = $this->get('kunstmaan_media.manager');
        $imageContext = $mediaManager->getContext($context);
        return $imageContext->getMetadataClass();
    }
}