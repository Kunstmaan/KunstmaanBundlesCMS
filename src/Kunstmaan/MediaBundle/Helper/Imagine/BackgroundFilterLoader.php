<?php

namespace Kunstmaan\MediaBundle\Helper\Imagine;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

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
        $background = new Color(
            isset($options['color']) ? $options['color'] : '#fff',
            isset($options['transparency']) ? $options['transparency'] : 0
        );
        $topLeft = new Point(0, 0);
        $size = $image->getSize();

        if (isset($options['size'])) {
            list($width, $height) = $options['size'];

            $size = new Box($width, $height);
            $topLeft = new Point(($width - $image->getSize()->getWidth()) / 2, ($height - $image->getSize()->getHeight()) / 2);
        }

        $canvas = $this->imagine->create($size, $background);

        return $canvas->paste($image, $topLeft);
    }
}
