<?php

namespace Kunstmaan\MediaBundle\Helper\Imagine;

use Imagine\Image\ImageInterface;

/**
 * This class can be removed when https://github.com/liip/LiipImagineBundle/issues/640 is fixed.
 *
 * @deprecated This class will be removed in MediaBundle 6.0 as the override is no longer necessary
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
