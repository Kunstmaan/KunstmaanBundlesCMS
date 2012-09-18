<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Kunstmaan\MediaBundle\Entity\File;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MediaBundle\Event\MediaEvent;
use Kunstmaan\MediaBundle\Event\Events;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata;
use Kunstmaan\MediaBundle\Helper\MediaManager;


/**
 * SlideController
 */
class SlideController extends Controller
{
    /**
     * @param int $mediaId
     *
     * @Route("/{mediaId}/edit", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_slide_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editAction($mediaId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Slide $slide */
        $slide = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $slide->setContent($slide->getUuid());

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('media', new SlideType());
        $bindingArray = array('media' => $slide);

        $metadataClass = $this->getMetadataClass(Slide::CONTEXT);
        if (isset($metadataClass)) {
            $classMetadata = $em->getClassMetadata($metadataClass);
            $repo = new EntityRepository($em, $classMetadata);

            $result = $repo->findByMedia($slide->getId());

            /* @var AbstractMediaMetadata $metadata */
            $metadata = null;
            if (!empty($result)) {
                $metadata = $result[0];
            } else {
                $metadata = new $metadataClass();
            }

            $formBuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingArray['metadata'] = $metadata;
        }

        $formBuilder->setData($bindingArray);
        $form = $formBuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $slide->setUuid($slide->getContent());

                $em->getRepository('KunstmaanMediaBundle:Media')->save($slide);

                if (isset($metadata)) {
                    $metadata->setMedia($slide);
                    $em->persist($metadata);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::POST_EDIT)) {
                    $event = new MediaEvent($slide, $metadata);
                    $dispatcher->dispatch(Events::POST_EDIT, $event);
                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'mediaId' => $slide->getId() )));
            }
        }

        return array(
            'form' => $form->createView(),
            'media' => $slide,
            'gallery' => $slide->getGallery()
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