<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AviaryController extends Controller
{

    /**
     * @Route("/aviary/{gallery_id}/{image_id}", requirements={"gallery_id" = "\d+", "image_id" = "\d+"}, name="KunstmaanMediaBundle_aviary")
     *
     * @param $gallery_id
     * @param $image_id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function indexAction($gallery_id, $image_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $gallery = $this->getImageGallery($gallery_id, $em);

        $helper = new MediaHelper();
        $helper->getMediaFromUrl($this->getRequest()->get('url'));

        $hulp = $this->getMedia($image_id, $em);
        $picture = new Image();
        $picture->setOriginal($hulp);
        $picture->setName($hulp->getName()."-edited");
        $picture->setContent($helper->getMedia());

        $picture->setGallery($gallery);

        $em->persist($picture);
        $em->flush();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));

    }

    protected function getMedia($media_id, \Doctrine\ORM\EntityManager $em)
    {
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);

        if (!$media) {
            throw $this->createNotFoundException('Unable to find picture.');
        }

        return $media;
    }

    protected function getImageGallery($gallery_id, \Doctrine\ORM\EntityManager $em)
    {
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:ImageGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find image gallery.');
        }

        return $imagegallery;
    }

}
