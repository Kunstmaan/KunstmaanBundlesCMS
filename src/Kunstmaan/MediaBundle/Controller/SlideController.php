<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MediaBundle\Helper\Event\MediaEvent;
use Kunstmaan\MediaBundle\Helper\Event\Events;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


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

        $slide = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $slide->setContent($slide->getUuid());
        $request = $this->getRequest();

        $formbuilder = $this->createFormBuilder();
        $formbuilder->add('media', new SlideType());
        $bindingarray = array('media' => $slide);

        $metadataClass = $this->getMetadataClass(Slide::CONTEXT);
        if (isset($metadataClass)) {
            $classMetadata = $em->getClassMetadata($metadataClass);
            $repo = new EntityRepository($em, $classMetadata);

            $result = $repo->findByMedia($slide->getId());

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
                $slide->setUuid($slide->getContent());

                $em->getRepository('KunstmaanMediaBundle:Media')->save($slide);

                if (isset($metadata)) {
                    $metadata->setMedia($slide);
                    $em->persist($metadata);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::POSTEDIT)) {
                    $event = new MediaEvent($slide, $metadata);
                    $dispatcher->dispatch(Events::POSTEDIT, $event);
                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'media_id' => $slide->getId() )));
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
        $mediaManager = $this->get('kunstmaan_media.manager');
        $imageContext = $mediaManager->getContext($context);

        return $imageContext->getMetadataClass();
    }
}