<?php

namespace Kunstmaan\MediaBundle\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kunstmaan\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * MediaRepository
 */
class MediaRepository extends EntityRepository
{
    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function save(Media $media)
    {
        $em->persist($media);
        $em->flush();
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function delete(Media $media)
    {
        $media->setDeleted(true);
        $em->persist($media);
        $em->flush();
    }

    /**
     * @param int $mediaId
     *
     * @return object
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getMedia($mediaId)
    {
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->find($mediaId);
        if (!$media) {
            throw new EntityNotFoundException('The id given for the media is not valid.');
        }

        return $media;
    }

    /**
     * @param integer $pictureId
     *
     * @return object
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getPicture($pictureId)
    {
        $picture = $em->getRepository('KunstmaanMediaBundle:Image')->find($pictureId);
        if (!$picture) {
            throw new EntityNotFoundException('Unable to find image.');
        }

        return $picture;
    }
}