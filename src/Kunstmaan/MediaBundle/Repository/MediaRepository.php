<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;

class MediaRepository
{
    /* @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Media $media)
    {
        $this->entityManager->persist($media);
        $this->entityManager->flush();
    }

    public function delete(Media $media)
    {
        $this->entityManager->remove($media);
        $this->entityManager->flush();
    }

    protected function getPicture($picture_id){
        $picture = $this->entityManager->getRepository('KunstmaanMediaBundle:Image')->find($picture_id);
        if (!$picture){
            throw new \Symfony\Component\Form\Exception\NotValidException('The id given is not valid');
        }

        return $picture;
    }

}