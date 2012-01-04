<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * controllerclass which Aviary can use to upload the edited image and add it to the database
 */
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

        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);

        $helper = new MediaHelper();
        $helper->getMediaFromUrl($this->getRequest()->get('url'));

        $hulp = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($image_id, $em);
        $picture = new Image();
        $picture->setOriginal($hulp);
        $picture->setName($hulp->getName()."-edited");
        $picture->setContent($helper->getMedia());

        $picture->setGallery($gallery);

        $em->persist($picture);
        $em->flush();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));

    }
}
