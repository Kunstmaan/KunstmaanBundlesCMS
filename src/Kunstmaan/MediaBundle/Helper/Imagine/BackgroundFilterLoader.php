<?php

namespace Kunstmaan\MediaBundle\Helper\Imagine;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

/**
 * This class can be removed when https://github.com/liip/LiipImagineBundle/issues/640 is fixed.
 */
class BackgroundFilterLoader extends \Liip\ImagineBundle\Imagine\Filter\Loader\BackgroundFilterLoader
{
    /**
     * {@inheritdoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        return parent::load($image, $options);
    }
}
