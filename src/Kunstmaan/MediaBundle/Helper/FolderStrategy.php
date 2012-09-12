<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\Locale\Exception\NotImplementedException;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Helper\MediaList\MediaListConfigurator;

/**
 * FolderStrategy
 */
class FolderStrategy implements GalleryStrategyInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Folder';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'folder';
    }

    /**
     * @return null
     */
    public function getNewBulkUploadMediaInstance()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getBulkUploadAccept()
    {
        return null;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\MediaBundle\Entity\Folder
     */
    public function getNewGallery(EntityManager $em)
    {
        return new Folder($em);
    }

    /**
     * @return string
     */
    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\Folder';
    }

    /**
     * @return null
     */
    public function getFormType()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getFormHelper()
    {
        return null;
    }

    /**
     * @param Folder $folder
     *
     * @return void
     */
    public function getListConfigurator(Folder $folder)
    {
        throw new NotImplementedException("you should override the getListConfigurator in your Strategy class " . get_class($this));
    }
}

