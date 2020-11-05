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
    public function save(Media $media)
    {
        $em = $this->getEntityManager();
        $em->persist($media);
        $em->flush();
    }

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
     *
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
     * Finds all Media  that has their deleted flag set to 1
     * and have their remove_from_file_system flag set to 0
     *
     * @return object[]
     */
    public function findAllDeleted()
    {
        return $this->findBy(['deleted' => true, 'removedFromFileSystem' => false]);
    }
}
