<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Kunstmaan\MediaBundle\Entity\Media;

class MediaMetadataController extends Controller
{
    /**
     * @Route("/{media_id}/edit", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_metadata_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($media_id, $em);
        $request = $this->getRequest();

        $metadataClass = $this->getMetadataClass($media->getContext());
        if (isset($metadataClass)) {
            $classMetadata = $em->getClassMetadata($metadataClass);
            $repo = new EntityRepository($em, $classMetadata);

            $result = $repo->findByMedia($media->getId());

            if(!empty($result)) {
                $metadata = $result[0];
            } else {
                $metadata = new $metadataClass();
            }

            $form = $this->createForm($metadata->getDefaultAdminType(), $metadata);
        } else {
            // return to show
            return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'media_id' => $media->getId() )));
        }

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){

                if (isset($metadata)) {
                    $metadata->setMedia($metadata);
                    $em->persist($metadata);
                    $em->flush();
                }

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'media_id' => $media->getId() )));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'form' => $form->createView(),
            'media' => $media,
            'gallery' => $media->getGallery(),
            'galleries' => $galleries
        );
    }

    private function getMetadataClass($context = File::CONTEXT)
    {
        $mediaManager = $this->get('kunstmaan_media.manager');
        $imageContext = $mediaManager->getContext($context);
        return $imageContext->getMetadataClass();
    }

}
