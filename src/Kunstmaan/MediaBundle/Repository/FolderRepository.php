<?php

namespace Kunstmaan\MediaBundle\Repository;

use Kunstmaan\MediaBundle\Entity\Folder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;

class FolderRepository extends EntityRepository
{
    public function save(Folder $gallery, EntityManager $em)
    {
        $em->persist($gallery);
        $em->flush();
    }

    public function delete(Folder $gallery, EntityManager $em)
    {
        $this->deleteFiles($gallery, $em);
        $this->deleteChildren($gallery, $em);
        $em->remove($gallery);
        $em->flush();
    }

    public function deleteFiles(Folder $gallery, EntityManager $em)
    {
        foreach ($gallery->getFiles() as $item) {
            $em->remove($item);
        }
    }

    public function deleteChildren(Folder $gallery, EntityManager $em)
    {
        foreach ($gallery->getChildren() as $child) {
            $this->deleteFiles($child, $em);
            $this->deleteChildren($child, $em);
            $em->remove($child);
        }
    }

    public function getAllFolders($limit = NULL)
    {
        $qb = $this->createQueryBuilder('folder')->select('folder')->where('folder.parent is null')->orderby('folder.sequencenumber');
        if (FALSE === is_null($limit)) $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getAllFoldersByType($limit = NULL)
    {
        $all    = $this->getAllFolders($limit);
        $bytype = array();
        foreach ($all as $gal) {
            if (!isset($bytype[$gal->getStrategy()->getType()])) $bytype[$gal->getStrategy()->getType()] = array();
            $bytype[$gal->getStrategy()->getType()][] = $gal;
        }
        return $bytype;
    }

    public function getFolder($folder_id, EntityManager $em)
    {
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->find($folder_id);
        if (!$folder) {
            throw new EntityNotFoundException('The id given for the folder is not valid.');
        }
        return $folder;
    }

}