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
     * @param Media $media
     */
    public function save(Media $media)
    {
        $em = $this->getEntityManager();

        $em->persist($media);
        $em->flush();
    }

    /**
     * @param Media $media
     */
    public function delete(Media $media)
    {
        $em = $this->getEntityManager();

        $media->setDeleted(true);
        $em->persist($media);
        $em->flush();
    }

    /**
     * @param int $mediaId
     *
     * @return object
     * @throws EntityNotFoundException
     */
    public function getMedia($mediaId)
    {
        $em = $this->getEntityManager();

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
     * @throws EntityNotFoundException
     */
    public function getPicture($pictureId)
    {
        $em = $this->getEntityManager();

        $picture = $em->getRepository('KunstmaanMediaBundle:Image')->find($pictureId);
        if (!$picture) {
            throw new EntityNotFoundException('Unable to find image.');
        }

        return $picture;
    }
}