<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;

interface GalleryStrategyInterface
{

    function getName();

    function getNewGallery(EntityManager $em);

    function getGalleryClassName();

    function getType();

    function getFormType();

    function getFormHelper();

    function getListConfigurator();

}