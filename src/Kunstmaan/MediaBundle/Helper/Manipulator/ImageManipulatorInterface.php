<?php

namespace Kunstmaan\MediaBundle\Helper\Manipulator;

use Kunstmaan\MediaBundle\Entity\Media;
use Gaufrette\File;

interface ImageManipulatorInterface
{
    const RESIZE_MODE_OUTBOUND = 'outbound';
    const RESIZE_MODE_INSET = 'inset';

    public function resize(Media $media, File $fromFile, File $toFile, $options = array());
}