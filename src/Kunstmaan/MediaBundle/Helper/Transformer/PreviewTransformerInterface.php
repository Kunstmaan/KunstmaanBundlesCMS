<?php

namespace Kunstmaan\MediaBundle\Helper\Transformer;

interface PreviewTransformerInterface
{
    /**
     * Return the path of the preview file.
     *
     * @param string $absolutePath
     *
     * @return string
     */
    public function getPreviewFilename($absolutePath);

    /**
     * Apply the transformer on the absolute path and return an altered version of it.
     *
     * @param string $absolutePath
     *
     * @return string
     */
    public function apply($absolutePath);
}
