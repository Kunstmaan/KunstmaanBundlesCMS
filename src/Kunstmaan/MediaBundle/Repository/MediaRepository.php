<?php

namespace Kunstmaan\MediaBundle\Repository;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\MediaBundle\Entity\Media;

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
        $media = $this->find($mediaId);
        if (!$media) {
            throw new EntityNotFoundException();
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
            throw new EntityNotFoundException();
        }

        return $picture;
    }
}
