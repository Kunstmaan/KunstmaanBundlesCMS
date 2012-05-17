<?php

namespace Kunstmaan\MediaBundle\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kunstmaan\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;

class MediaRepository extends EntityRepository
{
    public function save(Media $media, EntityManager $em)
    {
        $em->persist($media);
        $em->flush();
    }

    public function delete(Media $media, EntityManager $em)
    {
        $em->remove($media);
        $em->flush();
    }

    public function getMedia($media_id, EntityManager $em)
    {
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);
        if (!$media) {
            throw new EntityNotFoundException('The id given for the media is not valid.');
        }
        return $media;
    }

    public function getPicture($picture_id, EntityManager $em)
    {
        $picture = $em->getRepository('KunstmaanMediaBundle:Image')->find($picture_id);
        if (!$picture) {
            throw new EntityNotFoundException('Unable to find image.');
        }

        return $picture;
    }
}