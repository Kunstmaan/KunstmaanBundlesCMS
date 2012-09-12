<?php

namespace Kunstmaan\MediaBundle\Helper\Manipulator;

use Kunstmaan\MediaBundle\Entity\Media;
use Gaufrette\File;

/**
 * ImageManipulatorInterface
 */
interface ImageManipulatorInterface
{
    const RESIZE_MODE_OUTBOUND = 'outbound';
    const RESIZE_MODE_INSET = 'inset';

    /**
     * @param Media $media    Media
     * @param File  $fromFile From file
     * @param File  $toFile   To file
     * @param array $options  Options
     */
    public function resize(Media $media, File $fromFile, File $toFile, $options = array());
}