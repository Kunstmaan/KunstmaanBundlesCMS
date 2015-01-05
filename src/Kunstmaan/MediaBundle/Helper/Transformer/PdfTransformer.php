<?php

namespace Kunstmaan\MediaBundle\Helper\Transformer;

class PdfTransformer implements PreviewTransformerInterface
{
    /** @var \Imagick */
    protected $imagick;

    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
    }

    /**
     * Apply the transformer on the absolute path and return an altered version of it.
     *
     * @param string $absolutePath
     *
     * @return string|false
     */
    public function apply($absolutePath)
    {
        $info = pathinfo($absolutePath);

        if (isset($info['extension']) && false !== strpos(strtolower($info['extension']), 'pdf') && file_exists($absolutePath)) {
            // If it doesn't exist yet, extract the first page of the PDF
            $previewFilename = $this->getPreviewFilename($absolutePath);
            if (!file_exists($previewFilename)) {
                $this->imagick->readImage($absolutePath . '[0]');
                $this->imagick->setImageFormat('jpg');
                $this->imagick->flattenimages();
                $this->imagick->writeImage($previewFilename);
                $this->imagick->clear();
            }

            $absolutePath = $previewFilename;
        }

        return $absolutePath;
    }

    /**
     * @param string $absolutePath
     *
     * @return string
     */
    public function getPreviewFilename($absolutePath)
    {
        return $absolutePath . '.jpg';
    }
}

