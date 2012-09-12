<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

use Kunstmaan\MediaBundle\Entity\Folder;

use Symfony\Component\Form\AbstractType;

use Doctrine\ORM\EntityManager;

/**
 * GalleryStrategyInterface
 */
interface GalleryStrategyInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getNewBulkUploadMediaInstance();

    /**
     * @return string
     */
    public function getBulkUploadAccept();

    /**
     * @param EntityManager $em
     *
     * @return Folder
     */
    public function getNewGallery(EntityManager $em);

    /**
     * @return string
     */
    public function getGalleryClassName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return AbstractType
     */
    public function getFormType();

    /**
     * @return mixed
     */
    public function getFormHelper();

    /**
     * @param Folder $folder
     *
     * @return AbstractAdminListConfigurator
     */
    public function getListConfigurator(Folder $folder);

}