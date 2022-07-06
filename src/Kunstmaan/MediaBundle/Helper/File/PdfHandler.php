<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Transformer\PreviewTransformerInterface;

/**
 * Custom handler for PDF files (display thumbnails if imagemagick is enabled and has PDF support)
 */
class PdfHandler extends FileHandler
{
    const TYPE = 'pdf';

    /** @var string */
    protected $webPath;

    /** @var PreviewTransformerInterface */
    protected $pdfTransformer;

    /**
     * Inject the root dir so we know the full path where we need to store the file.
     *
     * @param string $kernelRootDir
     */
    public function setMediaPath($kernelRootDir)
    {
        parent::setMediaPath($kernelRootDir);

        $this->setWebPath(realpath(str_replace('/', DIRECTORY_SEPARATOR, $kernelRootDir . '/public/') . DIRECTORY_SEPARATOR));
    }

    /**
     * @param string $webPath
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;
    }

    public function setPdfTransformer(PreviewTransformerInterface $pdfTransformer)
    {
        $this->pdfTransformer = $pdfTransformer;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return PdfHandler::TYPE;
    }

    public function canHandle($object): bool
    {
        if (
            parent::canHandle($object) && (
                $object instanceof Media && $object->getContentType() == 'application/pdf'
            )
        ) {
            return true;
        }

        return false;
    }

    public function saveMedia(Media $media)
    {
        parent::saveMedia($media);

        try {
            // Generate preview for PDF
            $this->pdfTransformer->apply($this->webPath . $media->getUrl());
        } catch (\ImagickException $e) {
            // Fail silently ()
        }
    }

    /**
     * @param Media  $media    The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        $filename = $this->pdfTransformer->getPreviewFilename($basepath . $media->getUrl());
        if (!file_exists($this->webPath . $filename)) {
            return null;
        }

        return $filename;
    }
}
