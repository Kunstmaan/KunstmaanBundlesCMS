<?php

namespace Kunstmaan\KMediaBundle\Helper;

interface GalleryStrategyInterface
{

    function getName();

    function getNewGallery();

    function getGalleryClassName();

    function getType();

}