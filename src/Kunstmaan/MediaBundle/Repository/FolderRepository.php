<?php

namespace Kunstmaan\MediaBundle\Repository;

use Kunstmaan\MediaBundle\Entity\Folder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * FolderRepository
 */
class FolderRepository extends EntityRepository
{
    /**
     * @param Folder $gallery The gallery
     */
    public function save(Folder $gallery)
    {
        $em = $this->getEntityManager();

        $em->persist($gallery);
        $em->flush();
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Folder $gallery
     */
    public function delete(Folder $gallery)
    {
        $em = $this->getEntityManager();

        $this->deleteFiles($gallery, $em);
        $this->deleteChildren($gallery, $em);
        $gallery->setDeleted(true);
        $em->persist($gallery);
        $em->flush();
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Folder $gallery
     */
    public function deleteFiles(Folder $gallery)
    {
        $em = $this->getEntityManager();

        foreach ($gallery->getFiles() as $item) {
            $item->setDeleted(true);
            $em->persist($item);
            $em->remove($item);
        }
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Folder $gallery
     */
    public function deleteChildren(Folder $gallery)
    {
        $em = $this->getEntityManager();

        foreach ($gallery->getChildren() as $child) {
            $this->deleteFiles($child, $em);
            $this->deleteChildren($child, $em);
            $child->setDeleted(true);
            $em->persist($child);
        }
    }

    /**
     * @param null $limit
     *
     * @return array
     */
    public function getAllFolders($limit = null)
    {
        $qb = $this->createQueryBuilder('folder')->select('folder')->where('folder.parent is null AND folder.deleted != true')->orderby('folder.sequencenumber');
        if (false === is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param null $limit
     *
     * @return array
     */
    public function getAllFoldersByType($limit = null)
    {
        $all    = $this->getAllFolders($limit);
        $bytype = array();
        foreach ($all as $gal) {
            if (!isset($bytype[$gal->getStrategy()->getType()])) {
                $bytype[$gal->getStrategy()->getType()] = array();
            }
            $bytype[$gal->getStrategy()->getType()][] = $gal;
        }

        return $bytype;
    }

    /**
     * @param int $folderId
     *
     * @return object
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getFolder($folderId)
    {
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->find($folderId);
        if (!$folder) {
            throw new EntityNotFoundException('The id given for the folder is not valid.');
        }

        return $folder;
    }

}