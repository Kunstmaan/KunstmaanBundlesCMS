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
        $gallery = $this->getImageGallery($gallery_id);

        $helper = new MediaHelper();
        $helper->getMediaFromUrl($this->getRequest()->get('url'));

        $hulp = $this->getPicture($image_id);
        $picture = new Image();
        $picture->setOriginal($hulp);
        $picture->setName($hulp->getName()."-edited");
        $picture->setContent($helper->getMedia());

        $picture->setGallery($gallery);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($picture);
        $em->flush();

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                        ->getAllGalleries();

        $picturehelper = new MediaHelper();
        $form = $this->createForm(new \Kunstmaan\MediaBundle\Form\MediaType(), $picturehelper);

        $sub = new \Kunstmaan\MediaBundle\Entity\ImageGallery();
        $sub->setParent($gallery);
        $subform = $this->createForm(new \Kunstmaan\MediaBundle\Form\SubGalleryType(), $sub);

        return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries,
            'form' => $form->createView(),
            'subform' => $subform->createView()
        ));

    }

    protected function getPicture($picture_id){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $picture = $em->getRepository('KunstmaanMediaBundle:Image')->find($picture_id);

        if (!$picture) {
            throw $this->createNotFoundException('Unable to find picture.');
        }

        return $picture;
    }

    protected function getImageGallery($gallery_id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:ImageGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find image gallery.');
        }

        return $imagegallery;
    }

}
