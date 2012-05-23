<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;

interface GalleryStrategyInterface
{

    function getName();

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    function getNewBulkUploadMediaInstance();

    /**
     * @return string
     */
    function getBulkUploadAccept();

    function getNewGallery(EntityManager $em);

    function getGalleryClassName();

    function getType();

    function getFormType();

    function getFormHelper();

    function getListConfigurator();

}