<?php

namespace Kunstmaan\MediaBundle\Helper;

interface GalleryStrategyInterface
{

    function getName();

    function getNewGallery();

    function getGalleryClassName();

    function getType();

}