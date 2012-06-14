<?php

namespace Kunstmaan\MediaBundle\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kunstmaan\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;

class MediaRepository extends EntityRepository
{
    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function save(Media $media, EntityManager $em)
    {
        $em->persist($media);
        $em->flush();
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function delete(Media $media, EntityManager $em)
    {
        $media->setDeleted(true);
        $em->persist($media);
        $em->flush();
    }

    /**
     * @param $media_id
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return object
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getMedia($media_id, EntityManager $em)
    {
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);
        if (!$media) {
            throw new EntityNotFoundException('The id given for the media is not valid.');
        }
        return $media;
    }

    /**
     * @param $picture_id
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return object
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getPicture($picture_id, EntityManager $em)
    {
        $picture = $em->getRepository('KunstmaanMediaBundle:Image')->find($picture_id);
        if (!$picture) {
            throw new EntityNotFoundException('Unable to find image.');
        }

        return $picture;
    }
}