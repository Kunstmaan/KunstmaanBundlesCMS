<?php

namespace Kunstmaan\MediaBundle\Helper\Transformer;

use Liip\ImagineBundle\Imagine\Data\Transformer\TransformerInterface;

interface PreviewTransformerInterface extends TransformerInterface
{
    /**
     * Return the path of the preview file.
     *
     * @param string $absolutePath
     *
     * @return string
     */
    public function getPreviewFilename($absolutePath);
}
